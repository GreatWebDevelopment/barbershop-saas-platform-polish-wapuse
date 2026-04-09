import SwiftUI

struct AppointmentsView: View {
    @EnvironmentObject var auth: AuthManager
    @State private var appointments: [Appointment] = []
    @State private var loading = true
    @State private var showUpcoming = true

    var body: some View {
        NavigationStack {
            VStack {
                Picker("Filter", selection: $showUpcoming) {
                    Text("Upcoming").tag(true)
                    Text("Past").tag(false)
                }
                .pickerStyle(.segmented)
                .padding()

                if loading {
                    Spacer()
                    ProgressView().tint(Color(hex: "D4A853"))
                    Spacer()
                } else if filteredAppointments.isEmpty {
                    Spacer()
                    VStack(spacing: 12) {
                        Image(systemName: "calendar")
                            .font(.system(size: 48))
                            .foregroundColor(.gray)
                        Text("No \(showUpcoming ? "upcoming" : "past") appointments")
                            .foregroundColor(.gray)
                    }
                    Spacer()
                } else {
                    List(filteredAppointments) { appt in
                        AppointmentRow(appointment: appt, onCancel: {
                            Task { await cancelAppointment(appt) }
                        })
                        .listRowBackground(Color(hex: "1a1a2e"))
                    }
                    .listStyle(.plain)
                }
            }
            .background(Color(hex: "0f0f1a"))
            .navigationTitle("My Appointments")
            .task { await loadAppointments() }
            .refreshable { await loadAppointments() }
        }
    }

    var filteredAppointments: [Appointment] {
        let now = Date()
        return appointments.filter { appt in
            guard let date = appt.startsAtDate else { return false }
            if showUpcoming {
                return date > now && appt.status != "cancelled"
            } else {
                return date <= now || appt.status == "cancelled"
            }
        }
    }

    func loadAppointments() async {
        loading = true
        do {
            let resp: AppointmentsResponse = try await APIService.shared.get("/api/v1/me/appointments")
            appointments = resp.appointments
        } catch {}
        loading = false
    }

    func cancelAppointment(_ appt: Appointment) async {
        do {
            try await APIService.shared.delete("/api/v1/me/appointments/\(appt.id)")
            await loadAppointments()
        } catch {}
    }
}

struct AppointmentRow: View {
    let appointment: Appointment
    let onCancel: () -> Void

    var body: some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack {
                Text(appointment.service?.name ?? "Service")
                    .font(.headline).foregroundColor(.white)
                Spacer()
                StatusBadge(status: appointment.status)
            }

            if let staff = appointment.staff {
                Label(staff.name, systemImage: "person.fill")
                    .font(.subheadline).foregroundColor(.gray)
            }

            if let shop = appointment.shop {
                Label(shop.name, systemImage: "mappin")
                    .font(.caption).foregroundColor(.gray)
            }

            if let date = appointment.startsAtDate {
                Label(date.formatted(date: .abbreviated, time: .shortened), systemImage: "clock")
                    .font(.subheadline).foregroundColor(Color(hex: "D4A853"))
            }

            if let price = appointment.price {
                Text("$\(price)").font(.subheadline).bold().foregroundColor(.white)
            }

            if appointment.status == "scheduled", let date = appointment.startsAtDate, date > Date() {
                Button(action: onCancel) {
                    Text("Cancel")
                        .font(.caption).bold()
                        .foregroundColor(.red)
                        .padding(.horizontal, 12)
                        .padding(.vertical, 6)
                        .background(Color.red.opacity(0.15))
                        .cornerRadius(8)
                }
            }
        }
        .padding(.vertical, 4)
    }
}
