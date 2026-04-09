import Foundation

@MainActor
class AuthManager: ObservableObject {
    static let shared = AuthManager()

    @Published var currentUser: AppUser?
    @Published var isAuthenticated = false

    private let tokenKey = "auth_token"

    var token: String? {
        KeychainManager.load(key: tokenKey)
    }

    init() {
        if KeychainManager.load(key: tokenKey) != nil {
            isAuthenticated = true
            Task { await fetchProfile() }
        }
    }

    func login(email: String, password: String) async throws {
        let response: AuthResponse = try await APIService.shared.post("/api/v1/auth/login", body: [
            "email": email,
            "password": password
        ])
        KeychainManager.save(key: tokenKey, value: response.token)
        currentUser = response.user
        isAuthenticated = true
    }

    func register(name: String, email: String, password: String, phone: String?) async throws {
        var body: [String: String] = [
            "name": name,
            "email": email,
            "password": password,
            "password_confirmation": password
        ]
        if let phone { body["phone"] = phone }

        let response: AuthResponse = try await APIService.shared.post("/api/v1/auth/register", body: body)
        KeychainManager.save(key: tokenKey, value: response.token)
        currentUser = response.user
        isAuthenticated = true
    }

    func logout() async {
        try? await APIService.shared.post("/api/v1/auth/logout", body: [:]) as EmptyResponse
        KeychainManager.delete(key: tokenKey)
        currentUser = nil
        isAuthenticated = false
    }

    func fetchProfile() async {
        do {
            let response: UserResponse = try await APIService.shared.get("/api/v1/me")
            currentUser = response.user
        } catch {
            KeychainManager.delete(key: tokenKey)
            isAuthenticated = false
        }
    }
}
