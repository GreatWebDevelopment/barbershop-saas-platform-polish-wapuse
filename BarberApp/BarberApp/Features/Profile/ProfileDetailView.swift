import SwiftUI

struct ProfileDetailView: View {
    @EnvironmentObject var auth: AuthManager
    @State private var showLogin = false
    @State private var loyalty: LoyaltyResponse?

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 20) {
                    if auth.isAuthenticated, let user = auth.currentUser {
                        // User info
                        VStack(spacing: 8) {
                            Image(systemName: "person.circle.fill")
                                .font(.system(size: 60))
                                .foregroundColor(Color(hex: "D4A853"))
                            Text(user.name).font(.title2).bold().foregroundColor(.white)
                            Text(user.email).font(.subheadline).foregroundColor(.gray)
                            if let phone = user.phoneNumber {
                                Text(phone).font(.caption).foregroundColor(.gray)
                            }
                        }
                        .padding()

                        // Loyalty points
                        if let loyalty {
                            VStack(spacing: 8) {
                                Text("\(loyalty.totalPoints)")
                                    .font(.system(size: 48, weight: .bold))
                                    .foregroundColor(Color(hex: "D4A853"))
                                Text("Loyalty Points")
                                    .font(.headline).foregroundColor(.gray)
                            }
                            .frame(maxWidth: .infinity)
                            .padding()
                            .background(Color(hex: "1a1a2e"))
                            .cornerRadius(16)
                            .padding(.horizontal)

                            if !loyalty.rewards.isEmpty {
                                VStack(alignment: .leading, spacing: 8) {
                                    Text("Available Rewards").font(.headline).foregroundColor(.white)
                                    ForEach(loyalty.rewards) { reward in
                                        HStack {
                                            VStack(alignment: .leading) {
                                                Text(reward.name ?? "Reward").foregroundColor(.white)
                                                if let desc = reward.description {
                                                    Text(desc).font(.caption).foregroundColor(.gray)
                                                }
                                            }
                                            Spacer()
                                            Text("\(reward.pointsCost ?? 0) pts")
                                                .font(.subheadline).bold()
                                                .foregroundColor(Color(hex: "D4A853"))
                                        }
                                        .padding()
                                        .background(Color(hex: "1a1a2e"))
                                        .cornerRadius(8)
                                    }
                                }
                                .padding(.horizontal)
                            }
                        }

                        Button(action: { Task { await auth.logout() } }) {
                            Text("Sign Out")
                                .foregroundColor(.red)
                                .frame(maxWidth: .infinity)
                                .padding()
                                .background(Color.red.opacity(0.1))
                                .cornerRadius(12)
                        }
                        .padding(.horizontal)
                    } else {
                        VStack(spacing: 16) {
                            Image(systemName: "person.circle.fill")
                                .font(.system(size: 80))
                                .foregroundColor(Color(hex: "D4A853"))
                            Text("Guest User").font(.title2).bold().foregroundColor(.white)
                            Text("Sign in to book appointments and earn rewards").foregroundColor(.gray).multilineTextAlignment(.center)

                            Button(action: { showLogin = true }) {
                                Text("Sign In")
                                    .font(.headline).bold()
                                    .foregroundColor(Color(hex: "0f0f1a"))
                                    .frame(maxWidth: .infinity)
                                    .padding()
                                    .background(Color(hex: "D4A853"))
                                    .cornerRadius(12)
                            }
                            .padding(.horizontal)
                        }
                        .padding()
                    }
                }
            }
            .background(Color(hex: "0f0f1a"))
            .navigationTitle("Profile")
            .sheet(isPresented: $showLogin) {
                LoginView()
            }
            .task {
                if auth.isAuthenticated {
                    await loadLoyalty()
                }
            }
        }
    }

    func loadLoyalty() async {
        do {
            loyalty = try await APIService.shared.get("/api/v1/me/loyalty")
        } catch {}
    }
}
