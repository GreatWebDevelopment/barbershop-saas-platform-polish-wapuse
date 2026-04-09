import SwiftUI

struct BookingView: View {
    let shop: Shop
    @EnvironmentObject var auth: AuthManager
    @Environment(\.dismiss) var dismiss

    @State private var step = 0
    @State private var services: [ServiceDetail] = []
    @State private var staffMembers: [StaffMember] = []
    @State private var selectedService: ServiceDetail?
    @State private var selectedStaff: StaffMember?
    @State private var selectedDate = Date()
    @State private var availableSlots: [String] = []
    @State private var selectedSlot: String?
    @State private var loading = false
    @State private var error: String?
    @State private var booked = false

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 20) {
                    // Progress indicator
                    HStack(spacing: 8) {
                        ForEach(0..<4) { i in
                            Capsule()
                                .fill(i <= step ? Color(hex: "D4A853") : Color.gray.opacity(0.3))
                                .frame(height: 4)
                        }
                    }
                    .padding(.horizontal)

                    if booked {
                        bookingConfirmation
                    } else {
                        switch step {
                        case 0: serviceSelection
                        case 1: staffSelection
                        case 2: dateTimeSelection
                        case 3: confirmationStep
                        default: EmptyView()
                        }
                    }

                    if let error {
                        Text(error).foregroundColor(.red).font(.caption).padding(.horizontal)
                    }
                }
                .padding(.vertical)
            }
            .background(Color(hex: "0f0f1a"))
            .navigationTitle("Book Appointment")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") { dismiss() }
                }
            }
            .task { await loadServices() }
        }
    }

    var serviceSelection: some View {
        VStack(alignment: .leading, spacing: 12) {
            Text("Select a Service").font(.title2).bold().foregroundColor(.white).padding(.horizontal)

            ForEach(services) { service in
                Button(action: {
                    selectedService = service
                    step = 1
                    Task { await loadStaff() }
                }) {
                    HStack {
                        VStack(alignment: .leading, spacing: 4) {
                            Text(service.name).font(.headline).foregroundColor(.white)
                            if let desc = service.description {
                                Text(desc).font(.caption).foregroundColor(.gray).lineLimit(2)
                            }
                            Text("\(service.durationMinutes) min").font(.caption).foregroundColor(.gray)
                        }
                        Spacer()
                        Text("$\(service.price)").font(.title3).bold().foregroundColor(Color(hex: "D4A853"))
                    }
                    .padding()
                    .background(selectedService?.id == service.id ? Color(hex: "D4A853").opacity(0.2) : Color(hex: "1a1a2e"))
                    .cornerRadius(12)
                }
                .padding(.horizontal)
            }
        }
    }

    var staffSelection: some View {
        VStack(alignment: .leading, spacing: 12) {
            Text("Choose Your Stylist").font(.title2).bold().foregroundColor(.white).padding(.horizontal)

            ForEach(staffMembers) { member in
                Button(action: {
                    selectedStaff = member
                    step = 2
                }) {
                    HStack(spacing: 12) {
                        Image(systemName: "person.circle.fill")
                            .font(.system(size: 40))
                            .foregroundColor(Color(hex: "D4A853"))
                        VStack(alignment: .leading) {
                            Text(member.name).font(.headline).foregroundColor(.white)
                            if let title = member.title {
                                Text(title).font(.caption).foregroundColor(.gray)
                            }
                        }
                        Spacer()
                        Image(systemName: "chevron.right").foregroundColor(.gray)
                    }
                    .padding()
                    .background(Color(hex: "1a1a2e"))
                    .cornerRadius(12)
                }
                .padding(.horizontal)
            }

            Button(action: { step = 0 }) {
                Label("Back", systemImage: "chevron.left").foregroundColor(Color(hex: "D4A853"))
            }
            .padding(.horizontal)
        }
    }

    var dateTimeSelection: some View {
        VStack(alignment: .leading, spacing: 16) {
            Text("Pick Date & Time").font(.title2).bold().foregroundColor(.white).padding(.horizontal)

            DatePicker("Date", selection: $selectedDate, in: Date()..., displayedComponents: .date)
                .datePickerStyle(.graphical)
                .tint(Color(hex: "D4A853"))
                .padding(.horizontal)
                .onChange(of: selectedDate) { _, _ in
                    Task { await loadSlots() }
                }

            if loading {
                ProgressView().tint(Color(hex: "D4A853")).padding()
            } else if availableSlots.isEmpty {
                Text("No available slots for this date").foregroundColor(.gray).padding(.horizontal)
            } else {
                LazyVGrid(columns: Array(repeating: GridItem(.flexible()), count: 4), spacing: 8) {
                    ForEach(availableSlots, id: \.self) { slot in
                        Button(action: {
                            selectedSlot = slot
                            step = 3
                        }) {
                            Text(slot)
                                .font(.subheadline)
                                .padding(.vertical, 8)
                                .frame(maxWidth: .infinity)
                                .background(selectedSlot == slot ? Color(hex: "D4A853") : Color(hex: "1a1a2e"))
                                .foregroundColor(selectedSlot == slot ? Color(hex: "0f0f1a") : .white)
                                .cornerRadius(8)
                        }
                    }
                }
                .padding(.horizontal)
            }

            Button(action: { step = 1 }) {
                Label("Back", systemImage: "chevron.left").foregroundColor(Color(hex: "D4A853"))
            }
            .padding(.horizontal)
        }
        .task { await loadSlots() }
    }

    var confirmationStep: some View {
        VStack(spacing: 16) {
            Text("Confirm Booking").font(.title2).bold().foregroundColor(.white)

            VStack(alignment: .leading, spacing: 12) {
                confirmRow("Service", selectedService?.name ?? "")
                confirmRow("Stylist", selectedStaff?.name ?? "")
                confirmRow("Date", selectedDate.formatted(date: .abbreviated, time: .omitted))
                confirmRow("Time", selectedSlot ?? "")
                confirmRow("Price", "$\(selectedService?.price ?? "0")")
                confirmRow("Duration", "\(selectedService?.durationMinutes ?? 0) min")
            }
            .padding()
            .background(Color(hex: "1a1a2e"))
            .cornerRadius(12)
            .padding(.horizontal)

            Button(action: { Task { await bookAppointment() } }) {
                Text(loading ? "Booking..." : "Confirm Booking")
                    .font(.headline).bold()
                    .foregroundColor(Color(hex: "0f0f1a"))
                    .frame(maxWidth: .infinity)
                    .padding()
                    .background(Color(hex: "D4A853"))
                    .cornerRadius(12)
            }
            .disabled(loading)
            .padding(.horizontal)

            Button(action: { step = 2 }) {
                Label("Back", systemImage: "chevron.left").foregroundColor(Color(hex: "D4A853"))
            }
        }
    }

    var bookingConfirmation: some View {
        VStack(spacing: 20) {
            Image(systemName: "checkmark.circle.fill")
                .font(.system(size: 60))
                .foregroundColor(.green)
            Text("Booking Confirmed!")
                .font(.title).bold()
                .foregroundColor(.white)
            Text("Your appointment has been scheduled.")
                .foregroundColor(.gray)

            Button(action: { dismiss() }) {
                Text("Done")
                    .font(.headline)
                    .foregroundColor(Color(hex: "0f0f1a"))
                    .frame(maxWidth: .infinity)
                    .padding()
                    .background(Color(hex: "D4A853"))
                    .cornerRadius(12)
            }
            .padding(.horizontal)
        }
    }

    func confirmRow(_ label: String, _ value: String) -> some View {
        HStack {
            Text(label).foregroundColor(.gray)
            Spacer()
            Text(value).foregroundColor(.white).bold()
        }
    }

    func loadServices() async {
        do {
            let resp: ServicesResponse = try await APIService.shared.get("/api/v1/shops/\(shop.id)/services")
            services = resp.services
        } catch { self.error = "Failed to load services" }
    }

    func loadStaff() async {
        do {
            let resp: StaffListResponse = try await APIService.shared.get("/api/v1/shops/\(shop.id)/staff")
            staffMembers = resp.staff
        } catch { self.error = "Failed to load staff" }
    }

    func loadSlots() async {
        guard let service = selectedService, let staff = selectedStaff else { return }
        loading = true
        let dateStr = {
            let f = DateFormatter()
            f.dateFormat = "yyyy-MM-dd"
            return f.string(from: selectedDate)
        }()
        do {
            let resp: SlotsResponse = try await APIService.shared.get("/api/v1/availability/\(shop.id)", params: [
                "staff_id": "\(staff.id)",
                "date": dateStr,
                "service_id": "\(service.id)"
            ])
            availableSlots = resp.slots
            selectedSlot = nil
        } catch { self.error = "Failed to load slots" }
        loading = false
    }

    func bookAppointment() async {
        guard let service = selectedService, let staff = selectedStaff, let slot = selectedSlot else { return }
        loading = true
        error = nil
        let dateStr = {
            let f = DateFormatter()
            f.dateFormat = "yyyy-MM-dd"
            return f.string(from: selectedDate)
        }()

        do {
            if auth.isAuthenticated {
                let _: BookingResponse = try await APIService.shared.post("/api/v1/me/appointments", body: [
                    "shop_id": shop.id,
                    "service_id": service.id,
                    "staff_id": staff.id,
                    "date": dateStr,
                    "time": slot
                ])
            } else {
                let _: BookingResponse = try await APIService.shared.post("/api/v1/book/\(shop.id)", body: [
                    "service_id": service.id,
                    "staff_id": staff.id,
                    "date": dateStr,
                    "time": slot,
                    "customer_name": "Guest",
                    "customer_email": "guest@barber.app",
                    "customer_phone": "0000000000"
                ])
            }
            booked = true
        } catch let err as APIError {
            error = err.errorDescription
        } catch {
            self.error = "Booking failed"
        }
        loading = false
    }
}
