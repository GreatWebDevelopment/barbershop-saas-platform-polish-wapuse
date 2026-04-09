import Foundation

enum APIError: LocalizedError {
    case invalidURL
    case invalidResponse
    case httpError(Int, String)
    case decodingError(Error)

    var errorDescription: String? {
        switch self {
        case .invalidURL: return "Invalid URL"
        case .invalidResponse: return "Invalid response"
        case .httpError(let code, let msg): return "Error \(code): \(msg)"
        case .decodingError(let err): return "Decoding error: \(err.localizedDescription)"
        }
    }
}

class APIService: ObservableObject {
    static let shared = APIService()

    let baseURL: String

    init(baseURL: String = "") {
        self.baseURL = baseURL.isEmpty
            ? (ProcessInfo.processInfo.environment["API_BASE_URL"] ?? "https://barbershop-saas-platform-polish-wapuse.p.gwd.dev")
            : baseURL
    }

    private var authHeaders: [String: String] {
        var headers = ["Accept": "application/json", "Content-Type": "application/json"]
        if let token = KeychainManager.load(key: "auth_token") {
            headers["Authorization"] = "Bearer \(token)"
        }
        return headers
    }

    func get<T: Codable>(_ path: String, params: [String: String] = [:]) async throws -> T {
        var components = URLComponents(string: baseURL + path)!
        if !params.isEmpty {
            components.queryItems = params.map { URLQueryItem(name: $0.key, value: $0.value) }
        }
        guard let url = components.url else { throw APIError.invalidURL }

        var request = URLRequest(url: url)
        authHeaders.forEach { request.setValue($0.value, forHTTPHeaderField: $0.key) }

        let (data, response) = try await URLSession.shared.data(for: request)
        try validateResponse(response, data: data)

        do {
            return try JSONDecoder().decode(T.self, from: data)
        } catch {
            throw APIError.decodingError(error)
        }
    }

    @discardableResult
    func post<T: Codable>(_ path: String, body: [String: Any]) async throws -> T {
        guard let url = URL(string: baseURL + path) else { throw APIError.invalidURL }

        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        authHeaders.forEach { request.setValue($0.value, forHTTPHeaderField: $0.key) }
        request.httpBody = try JSONSerialization.data(withJSONObject: body)

        let (data, response) = try await URLSession.shared.data(for: request)
        try validateResponse(response, data: data)

        do {
            return try JSONDecoder().decode(T.self, from: data)
        } catch {
            throw APIError.decodingError(error)
        }
    }

    func delete(_ path: String) async throws {
        guard let url = URL(string: baseURL + path) else { throw APIError.invalidURL }

        var request = URLRequest(url: url)
        request.httpMethod = "DELETE"
        authHeaders.forEach { request.setValue($0.value, forHTTPHeaderField: $0.key) }

        let (data, response) = try await URLSession.shared.data(for: request)
        try validateResponse(response, data: data)
    }

    func patch<T: Codable>(_ path: String, body: [String: Any]) async throws -> T {
        guard let url = URL(string: baseURL + path) else { throw APIError.invalidURL }

        var request = URLRequest(url: url)
        request.httpMethod = "PATCH"
        authHeaders.forEach { request.setValue($0.value, forHTTPHeaderField: $0.key) }
        request.httpBody = try JSONSerialization.data(withJSONObject: body)

        let (data, response) = try await URLSession.shared.data(for: request)
        try validateResponse(response, data: data)

        do {
            return try JSONDecoder().decode(T.self, from: data)
        } catch {
            throw APIError.decodingError(error)
        }
    }

    // MARK: - Legacy methods for backward compatibility

    func fetchNearbyLocations(lat: Double, lng: Double, radius: Double = 25) async throws -> [Shop] {
        let response: LocationsResponse = try await get("/api/v1/locations/nearby", params: [
            "lat": "\(lat)", "lng": "\(lng)", "radius": "\(radius)"
        ])
        return response.locations
    }

    func checkIn(shopId: Int, name: String, phone: String, partySize: Int = 1, serviceId: Int? = nil, stylistPreference: String = "first_available") async throws -> CheckInResponse {
        var body: [String: Any] = [
            "shop_id": shopId,
            "customer_name": name,
            "customer_phone": phone,
            "party_size": partySize,
            "stylist_preference": stylistPreference,
        ]
        if let serviceId { body["service_id"] = serviceId }
        return try await post("/api/v1/queue/check-in", body: body)
    }

    func getQueueStatus(queueNumber: String) async throws -> QueueStatusResponse {
        return try await get("/api/v1/queue/\(queueNumber)")
    }

    func cancelCheckIn(queueNumber: String) async throws {
        try await delete("/api/v1/queue/\(queueNumber)")
    }

    private func validateResponse(_ response: URLResponse, data: Data) throws {
        guard let http = response as? HTTPURLResponse else { throw APIError.invalidResponse }
        guard (200...299).contains(http.statusCode) else {
            let msg = (try? JSONDecoder().decode([String: String].self, from: data))?["message"] ?? "Unknown error"
            throw APIError.httpError(http.statusCode, msg)
        }
    }
}
