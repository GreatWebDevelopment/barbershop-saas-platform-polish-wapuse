import SwiftUI

struct LoginView: View {
    @EnvironmentObject var auth: AuthManager
    @State private var email = ""
    @State private var password = ""
    @State private var loading = false
    @State private var error: String?
    @State private var showRegister = false

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 24) {
                    Image(systemName: "scissors")
                        .font(.system(size: 60))
                        .foregroundColor(Color(hex: "D4A853"))
                        .padding(.top, 40)

                    Text("BarberPro")
                        .font(.largeTitle).bold()
                        .foregroundColor(.white)

                    VStack(spacing: 16) {
                        TextField("Email", text: $email)
                            .textFieldStyle(.roundedBorder)
                            .textInputAutocapitalization(.never)
                            .keyboardType(.emailAddress)

                        SecureField("Password", text: $password)
                            .textFieldStyle(.roundedBorder)

                        if let error {
                            Text(error)
                                .foregroundColor(.red)
                                .font(.caption)
                        }

                        Button(action: { Task { await login() } }) {
                            Text(loading ? "Signing In..." : "Sign In")
                                .font(.headline).bold()
                                .foregroundColor(Color(hex: "0f0f1a"))
                                .frame(maxWidth: .infinity)
                                .padding()
                                .background(Color(hex: "D4A853"))
                                .cornerRadius(12)
                        }
                        .disabled(email.isEmpty || password.isEmpty || loading)

                        Button("Create Account") {
                            showRegister = true
                        }
                        .foregroundColor(Color(hex: "D4A853"))
                    }
                    .padding()
                    .background(Color(hex: "1a1a2e"))
                    .cornerRadius(16)
                    .padding(.horizontal)

                    Button("Continue as Guest") {
                        // Just dismiss - guest mode is default
                    }
                    .foregroundColor(.gray)
                }
            }
            .background(Color(hex: "0f0f1a"))
            .sheet(isPresented: $showRegister) {
                RegisterView()
            }
        }
    }

    func login() async {
        loading = true
        error = nil
        do {
            try await auth.login(email: email, password: password)
        } catch let err as APIError {
            error = err.errorDescription
        } catch {
            self.error = "Login failed. Please try again."
        }
        loading = false
    }
}
