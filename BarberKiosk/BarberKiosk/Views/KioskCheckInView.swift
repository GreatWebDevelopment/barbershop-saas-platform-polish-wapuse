import SwiftUI

struct KioskCheckInView: View {
    let shopId: Int
    @State private var step = 0 // 0: welcome, 1: name, 2: phone, 3: party, 4: service, 5: success
    @State private var name = ""
    @State private var phone = ""
    @State private var partySize = 1
    @State private var loading = false
    @State private var queueNumber = ""
    @State private var position = 0
    @State private var waitMinutes = 0
    @State private var resetTimer: Timer?

    private let goldColor = Color(red: 212/255, green: 168/255, blue: 83/255)
    private let darkBg = Color(red: 15/255, green: 15/255, blue: 26/255)
    private let cardBg = Color(red: 26/255, green: 26/255, blue: 46/255)

    var body: some View {
        ZStack {
            darkBg.ignoresSafeArea()

            VStack(spacing: 0) {
                // Header
                HStack {
                    Text("Barber").font(.system(size: 32, weight: .black)).foregroundColor(.white)
                    + Text("Pro").font(.system(size: 32, weight: .black)).foregroundColor(goldColor)
                    Spacer()
                    if step > 0 && step < 5 {
                        Text("Step \(step) of 4")
                            .foregroundColor(.gray)
                    }
                }
                .padding(.horizontal, 40)
                .padding(.top, 20)
                .padding(.bottom, 10)

                // Progress bar
                if step > 0 && step < 5 {
                    HStack(spacing: 8) {
                        ForEach(1...4, id: \.self) { i in
                            RoundedRectangle(cornerRadius: 4)
                                .fill(i <= step ? goldColor : Color.gray.opacity(0.3))
                                .frame(height: 6)
                        }
                    }
                    .padding(.horizontal, 40)
                    .padding(.bottom, 20)
                }

                Spacer()

                // Content
                Group {
                    switch step {
                    case 0: welcomeStep
                    case 1: nameStep
                    case 2: phoneStep
                    case 3: partyStep
                    case 4: confirmStep
                    case 5: successStep
                    default: welcomeStep
                    }
                }
                .padding(.horizontal, 60)

                Spacer()
            }
        }
        .onAppear { startIdleTimer() }
    }

    // MARK: - Steps

    var welcomeStep: some View {
        VStack(spacing: 30) {
            Image(systemName: "scissors")
                .font(.system(size: 80))
                .foregroundColor(goldColor)
            Text("Welcome!")
                .font(.system(size: 48, weight: .bold))
                .foregroundColor(.white)
            Text("Tap below to check in and join the queue")
                .font(.title2)
                .foregroundColor(.gray)
            Button(action: { withAnimation { step = 1 } }) {
                Text("Check In")
                    .font(.title.bold())
                    .foregroundColor(darkBg)
                    .frame(maxWidth: 400)
                    .padding(.vertical, 20)
                    .background(goldColor)
                    .cornerRadius(16)
            }
        }
    }

    var nameStep: some View {
        VStack(spacing: 24) {
            Text("What's your name?")
                .font(.system(size: 36, weight: .bold))
                .foregroundColor(.white)
            TextField("Enter your name", text: $name)
                .font(.title)
                .textFieldStyle(.roundedBorder)
                .frame(maxWidth: 500)
            navigationButtons(canProceed: !name.isEmpty)
        }
    }

    var phoneStep: some View {
        VStack(spacing: 24) {
            Text("Your phone number")
                .font(.system(size: 36, weight: .bold))
                .foregroundColor(.white)
            Text("We'll text you when it's your turn")
                .foregroundColor(.gray)
            TextField("(555) 123-4567", text: $phone)
                .font(.title)
                .textFieldStyle(.roundedBorder)
                .keyboardType(.phonePad)
                .frame(maxWidth: 500)
            navigationButtons(canProceed: !phone.isEmpty)
        }
    }

    var partyStep: some View {
        VStack(spacing: 24) {
            Text("How many people?")
                .font(.system(size: 36, weight: .bold))
                .foregroundColor(.white)
            HStack(spacing: 16) {
                ForEach(1...5, id: \.self) { n in
                    Button(action: { partySize = n }) {
                        Text("\(n)")
                            .font(.system(size: 32, weight: .bold))
                            .foregroundColor(partySize == n ? darkBg : .white)
                            .frame(width: 80, height: 80)
                            .background(partySize == n ? goldColor : cardBg)
                            .cornerRadius(16)
                    }
                }
            }
            navigationButtons(canProceed: true)
        }
    }

    var confirmStep: some View {
        VStack(spacing: 24) {
            Text("Confirm Check-In")
                .font(.system(size: 36, weight: .bold))
                .foregroundColor(.white)
            VStack(spacing: 12) {
                HStack { Text("Name:").foregroundColor(.gray); Spacer(); Text(name).foregroundColor(.white) }
                HStack { Text("Phone:").foregroundColor(.gray); Spacer(); Text(phone).foregroundColor(.white) }
                HStack { Text("Party:").foregroundColor(.gray); Spacer(); Text("\(partySize) \(partySize == 1 ? "person" : "people")").foregroundColor(.white) }
            }
            .font(.title3)
            .padding(24)
            .background(cardBg)
            .cornerRadius(16)
            .frame(maxWidth: 500)

            HStack(spacing: 16) {
                Button(action: { withAnimation { step -= 1 } }) {
                    Text("Back")
                        .font(.title3.bold())
                        .foregroundColor(.gray)
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 16)
                        .background(cardBg)
                        .cornerRadius(12)
                }
                Button(action: { Task { await submitCheckIn() } }) {
                    Text(loading ? "Checking In..." : "Check In")
                        .font(.title3.bold())
                        .foregroundColor(darkBg)
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 16)
                        .background(goldColor)
                        .cornerRadius(12)
                }
                .disabled(loading)
            }
            .frame(maxWidth: 500)
        }
    }

    var successStep: some View {
        VStack(spacing: 24) {
            Image(systemName: "checkmark.circle.fill")
                .font(.system(size: 80))
                .foregroundColor(.green)
            Text("You're Checked In!")
                .font(.system(size: 40, weight: .bold))
                .foregroundColor(.white)
            Text(queueNumber)
                .font(.system(size: 72, weight: .black))
                .foregroundColor(goldColor)
            HStack(spacing: 40) {
                VStack {
                    Text("#\(position)").font(.system(size: 36, weight: .bold)).foregroundColor(.white)
                    Text("Position").foregroundColor(.gray)
                }
                VStack {
                    Text("~\(waitMinutes)m").font(.system(size: 36, weight: .bold)).foregroundColor(.white)
                    Text("Est. Wait").foregroundColor(.gray)
                }
            }
            Text("Screen will reset in 15 seconds")
                .foregroundColor(.gray)
                .font(.caption)
        }
        .onAppear {
            DispatchQueue.main.asyncAfter(deadline: .now() + 15) {
                resetForm()
            }
        }
    }

    // MARK: - Helpers

    func navigationButtons(canProceed: Bool) -> some View {
        HStack(spacing: 16) {
            Button(action: { withAnimation { step -= 1 } }) {
                Text("Back")
                    .font(.title3.bold())
                    .foregroundColor(.gray)
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 16)
                    .background(cardBg)
                    .cornerRadius(12)
            }
            Button(action: { withAnimation { step += 1 } }) {
                Text("Continue")
                    .font(.title3.bold())
                    .foregroundColor(canProceed ? darkBg : .gray)
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 16)
                    .background(canProceed ? goldColor : Color.gray.opacity(0.3))
                    .cornerRadius(12)
            }
            .disabled(!canProceed)
        }
        .frame(maxWidth: 500)
    }

    func submitCheckIn() async {
        loading = true
        do {
            let baseURL = ProcessInfo.processInfo.environment["API_BASE_URL"] ?? "https://barbershop-saas-platform-polish.p.gwd.dev"
            let url = URL(string: "\(baseURL)/api/v1/queue/check-in")!
            var request = URLRequest(url: url)
            request.httpMethod = "POST"
            request.setValue("application/json", forHTTPHeaderField: "Content-Type")
            let body: [String: Any] = [
                "shop_id": shopId,
                "customer_name": name,
                "customer_phone": phone,
                "party_size": partySize,
                "stylist_preference": "first_available"
            ]
            request.httpBody = try JSONSerialization.data(withJSONObject: body)
            let (data, _) = try await URLSession.shared.data(for: request)
            if let json = try JSONSerialization.jsonObject(with: data) as? [String: Any] {
                queueNumber = json["queue_number"] as? String ?? ""
                position = json["position"] as? Int ?? 0
                waitMinutes = json["estimated_wait_minutes"] as? Int ?? 0
            }
            withAnimation { step = 5 }
        } catch {
            print("Check-in error: \(error)")
        }
        loading = false
    }

    func resetForm() {
        withAnimation {
            step = 0
            name = ""
            phone = ""
            partySize = 1
            queueNumber = ""
            position = 0
            waitMinutes = 0
        }
    }

    func startIdleTimer() {
        // Auto-reset after 2 min of inactivity on welcome screen
    }
}
