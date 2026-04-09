import SwiftUI

struct RegisterView: View {
    @EnvironmentObject var auth: AuthManager
    @Environment(\.dismiss) var dismiss
    @State private var name = ""
    @State private var email = ""
    @State private var phone = ""
    @State private var password = ""
    @State private var loading = false
    @State private var error: String?

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 16) {
                    TextField("Full Name", text: $name)
                        .textFieldStyle(.roundedBorder)

                    TextField("Email", text: $email)
                        .textFieldStyle(.roundedBorder)
                        .textInputAutocapitalization(.never)
                        .keyboardType(.emailAddress)

                    TextField("Phone (optional)", text: $phone)
                        .textFieldStyle(.roundedBorder)
                        .keyboardType(.phonePad)

                    SecureField("Password (min 8 characters)", text: $password)
                        .textFieldStyle(.roundedBorder)

                    if let error {
                        Text(error).foregroundColor(.red).font(.caption)
                    }

                    Button(action: { Task { await register() } }) {
                        Text(loading ? "Creating Account..." : "Create Account")
                            .font(.headline).bold()
                            .foregroundColor(Color(hex: "0f0f1a"))
                            .frame(maxWidth: .infinity)
                            .padding()
                            .background(Color(hex: "D4A853"))
                            .cornerRadius(12)
                    }
                    .disabled(name.isEmpty || email.isEmpty || password.count < 8 || loading)
                }
                .padding()
                .background(Color(hex: "1a1a2e"))
                .cornerRadius(16)
                .padding()
            }
            .background(Color(hex: "0f0f1a"))
            .navigationTitle("Create Account")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") { dismiss() }
                }
            }
        }
    }

    func register() async {
        loading = true
        error = nil
        do {
            try await auth.register(name: name, email: email, password: password, phone: phone.isEmpty ? nil : phone)
            dismiss()
        } catch let err as APIError {
            error = err.errorDescription
        } catch {
            self.error = "Registration failed."
        }
        loading = false
    }
}
