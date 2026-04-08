import SwiftUI
import MapKit

struct ContentView: View {
    @EnvironmentObject var appState: AppState
    @State private var selectedTab = 0

    var body: some View {
        TabView(selection: $selectedTab) {
            LocationsView()
                .tabItem {
                    Label("Locations", systemImage: "mappin.circle.fill")
                }
                .tag(0)

            if let entry = appState.currentQueueEntry {
                QueueStatusView(queueNumber: entry.queueNumber)
                    .tabItem {
                        Label("My Queue", systemImage: "person.line.dotted.person.fill")
                    }
                    .tag(1)
            }

            ProfileView()
                .tabItem {
                    Label("Profile", systemImage: "person.circle")
                }
                .tag(2)
        }
        .tint(Color(hex: "D4A853"))
    }
}

// MARK: - Locations View
struct LocationsView: View {
    @StateObject private var api = APIService.shared
    @State private var locations: [Shop] = []
    @State private var region = MKCoordinateRegion(
        center: CLLocationCoordinate2D(latitude: 30.2672, longitude: -97.7431),
        span: MKCoordinateSpan(latitudeDelta: 0.3, longitudeDelta: 0.3)
    )
    @State private var showMap = true
    @State private var loading = false

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                // Toggle map/list
                Picker("View", selection: $showMap) {
                    Text("Map").tag(true)
                    Text("List").tag(false)
                }
                .pickerStyle(.segmented)
                .padding()

                if showMap {
                    Map(coordinateRegion: $region, annotationItems: locations.filter { $0.coordinate != nil }) { shop in
                        MapAnnotation(coordinate: shop.coordinate!) {
                            NavigationLink(destination: LocationDetailView(shop: shop)) {
                                VStack(spacing: 2) {
                                    Image(systemName: "scissors")
                                        .font(.title2)
                                        .foregroundColor(.white)
                                        .padding(8)
                                        .background(Color(hex: "D4A853"))
                                        .clipShape(Circle())
                                    Text("\(shop.waitTime ?? 0)m")
                                        .font(.caption2).bold()
                                        .foregroundColor(.white)
                                        .padding(.horizontal, 6)
                                        .padding(.vertical, 2)
                                        .background(Color.black.opacity(0.7))
                                        .cornerRadius(4)
                                }
                            }
                        }
                    }
                } else {
                    List(locations) { shop in
                        NavigationLink(destination: LocationDetailView(shop: shop)) {
                            LocationRow(shop: shop)
                        }
                        .listRowBackground(Color(hex: "1a1a2e"))
                    }
                    .listStyle(.plain)
                }
            }
            .navigationTitle("Find a Location")
            .background(Color(hex: "0f0f1a"))
            .task {
                await loadLocations()
            }
        }
    }

    func loadLocations() async {
        loading = true
        do {
            locations = try await APIService.shared.fetchNearbyLocations(lat: 30.2672, lng: -97.7431, radius: 50)
        } catch {
            print("Error loading locations: \(error)")
        }
        loading = false
    }
}

struct LocationRow: View {
    let shop: Shop

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text(shop.name).font(.headline).foregroundColor(.white)
            Text(shop.fullAddress).font(.caption).foregroundColor(.gray)
            HStack {
                Label("\(shop.waitingCount ?? 0) waiting", systemImage: "person.2")
                    .font(.caption)
                    .foregroundColor(.orange)
                Spacer()
                Text("~\(shop.waitTime ?? 0) min")
                    .font(.caption).bold()
                    .foregroundColor(Color(hex: "D4A853"))
            }
        }
        .padding(.vertical, 4)
    }
}

// MARK: - Location Detail
struct LocationDetailView: View {
    let shop: Shop
    @State private var navigateToCheckIn = false

    var body: some View {
        ScrollView {
            VStack(spacing: 20) {
                // Stats
                HStack(spacing: 16) {
                    StatCard(value: "\(shop.waitingCount ?? 0)", label: "In Line", color: .orange)
                    StatCard(value: "~\(shop.waitTime ?? 0)m", label: "Wait", color: Color(hex: "D4A853"))
                    StatCard(value: shop.phone ?? "N/A", label: "Phone", color: .blue)
                }
                .padding(.horizontal)

                // Check In Button
                NavigationLink(destination: CheckInView(shop: shop)) {
                    Text("Check In Now")
                        .font(.headline).bold()
                        .foregroundColor(Color(hex: "0f0f1a"))
                        .frame(maxWidth: .infinity)
                        .padding()
                        .background(Color(hex: "D4A853"))
                        .cornerRadius(12)
                }
                .padding(.horizontal)

                // Address
                VStack(alignment: .leading, spacing: 8) {
                    Label(shop.fullAddress, systemImage: "mappin.circle")
                        .foregroundColor(.gray)
                }
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding()
                .background(Color(hex: "1a1a2e"))
                .cornerRadius(12)
                .padding(.horizontal)
            }
            .padding(.vertical)
        }
        .navigationTitle(shop.name)
        .background(Color(hex: "0f0f1a"))
    }
}

struct StatCard: View {
    let value: String
    let label: String
    let color: Color

    var body: some View {
        VStack(spacing: 4) {
            Text(value).font(.title2).bold().foregroundColor(color)
            Text(label).font(.caption).foregroundColor(.gray)
        }
        .frame(maxWidth: .infinity)
        .padding()
        .background(Color(hex: "1a1a2e"))
        .cornerRadius(12)
    }
}

// MARK: - Check In View
struct CheckInView: View {
    let shop: Shop
    @EnvironmentObject var appState: AppState
    @State private var name = ""
    @State private var phone = ""
    @State private var partySize = 1
    @State private var loading = false
    @State private var result: CheckInResponse?
    @State private var error: String?

    var body: some View {
        ScrollView {
            VStack(spacing: 24) {
                if let result = result {
                    // Success
                    VStack(spacing: 16) {
                        Image(systemName: "checkmark.circle.fill")
                            .font(.system(size: 60))
                            .foregroundColor(.green)
                        Text("You're Checked In!")
                            .font(.title).bold()
                            .foregroundColor(.white)
                        Text(result.queueNumber)
                            .font(.system(size: 48, weight: .black))
                            .foregroundColor(Color(hex: "D4A853"))
                        HStack(spacing: 24) {
                            VStack {
                                Text("#\(result.position)").font(.title2).bold().foregroundColor(.white)
                                Text("Position").font(.caption).foregroundColor(.gray)
                            }
                            VStack {
                                Text("~\(result.estimatedWaitMinutes)m").font(.title2).bold().foregroundColor(.white)
                                Text("Est. Wait").font(.caption).foregroundColor(.gray)
                            }
                        }
                    }
                    .padding()
                    .background(Color(hex: "1a1a2e"))
                    .cornerRadius(16)
                } else {
                    // Form
                    VStack(alignment: .leading, spacing: 16) {
                        Text("Your Name")
                            .font(.subheadline).foregroundColor(.gray)
                        TextField("John Smith", text: $name)
                            .textFieldStyle(.roundedBorder)

                        Text("Phone Number")
                            .font(.subheadline).foregroundColor(.gray)
                        TextField("(555) 123-4567", text: $phone)
                            .textFieldStyle(.roundedBorder)
                            .keyboardType(.phonePad)

                        Text("Party Size")
                            .font(.subheadline).foregroundColor(.gray)
                        Picker("Party Size", selection: $partySize) {
                            ForEach(1...5, id: \.self) { Text("\($0)").tag($0) }
                        }
                        .pickerStyle(.segmented)

                        if let error = error {
                            Text(error).foregroundColor(.red).font(.caption)
                        }

                        Button(action: { Task { await checkIn() } }) {
                            Text(loading ? "Checking In..." : "Check In")
                                .font(.headline).bold()
                                .foregroundColor(Color(hex: "0f0f1a"))
                                .frame(maxWidth: .infinity)
                                .padding()
                                .background(name.isEmpty || phone.isEmpty ? Color.gray : Color(hex: "D4A853"))
                                .cornerRadius(12)
                        }
                        .disabled(name.isEmpty || phone.isEmpty || loading)
                    }
                    .padding()
                    .background(Color(hex: "1a1a2e"))
                    .cornerRadius(16)
                }
            }
            .padding()
        }
        .navigationTitle("Check In — \(shop.name)")
        .background(Color(hex: "0f0f1a"))
    }

    func checkIn() async {
        loading = true
        error = nil
        do {
            let response = try await APIService.shared.checkIn(
                shopId: shop.id, name: name, phone: phone, partySize: partySize
            )
            result = response
            appState.currentQueueEntry = response.queueEntry
        } catch {
            self.error = "Failed to check in. Please try again."
        }
        loading = false
    }
}

// MARK: - Queue Status View
struct QueueStatusView: View {
    let queueNumber: String
    @State private var status: QueueStatusResponse?
    @State private var timer: Timer?

    var body: some View {
        NavigationStack {
            ScrollView {
                if let status = status {
                    VStack(spacing: 20) {
                        Text(status.queueEntry.queueNumber)
                            .font(.system(size: 48, weight: .black))
                            .foregroundColor(Color(hex: "D4A853"))

                        StatusBadge(status: status.queueEntry.status)

                        if status.queueEntry.status == "waiting" {
                            HStack(spacing: 24) {
                                VStack {
                                    Text("#\(status.queueEntry.position)").font(.title).bold().foregroundColor(.white)
                                    Text("Position").font(.caption).foregroundColor(.gray)
                                }
                                VStack {
                                    Text("~\(status.estimatedWaitMinutes)m").font(.title).bold().foregroundColor(.white)
                                    Text("Wait").font(.caption).foregroundColor(.gray)
                                }
                                VStack {
                                    Text("\(status.peopleAhead)").font(.title).bold().foregroundColor(.white)
                                    Text("Ahead").font(.caption).foregroundColor(.gray)
                                }
                            }
                        }
                    }
                    .padding()
                } else {
                    ProgressView().tint(Color(hex: "D4A853"))
                }
            }
            .navigationTitle("Queue Status")
            .background(Color(hex: "0f0f1a"))
            .task {
                await refresh()
                timer = Timer.scheduledTimer(withTimeInterval: 10, repeats: true) { _ in
                    Task { await refresh() }
                }
            }
            .onDisappear { timer?.invalidate() }
        }
    }

    func refresh() async {
        do {
            status = try await APIService.shared.getQueueStatus(queueNumber: queueNumber)
        } catch {}
    }
}

struct StatusBadge: View {
    let status: String

    var config: (String, Color) {
        switch status {
        case "waiting": return ("In Line", .orange)
        case "called": return ("Your Turn!", .green)
        case "in_service": return ("In Service", .blue)
        case "completed": return ("Done", .gray)
        default: return (status.capitalized, .gray)
        }
    }

    var body: some View {
        Text(config.0)
            .font(.headline).bold()
            .foregroundColor(config.1)
            .padding(.horizontal, 20)
            .padding(.vertical, 10)
            .background(config.1.opacity(0.15))
            .cornerRadius(20)
    }
}

// MARK: - Profile View
struct ProfileView: View {
    var body: some View {
        NavigationStack {
            VStack(spacing: 20) {
                Image(systemName: "person.circle.fill")
                    .font(.system(size: 80))
                    .foregroundColor(Color(hex: "D4A853"))
                Text("Guest User")
                    .font(.title2).bold()
                    .foregroundColor(.white)
                Text("Sign in to save your preferences")
                    .foregroundColor(.gray)
            }
            .frame(maxWidth: .infinity, maxHeight: .infinity)
            .background(Color(hex: "0f0f1a"))
            .navigationTitle("Profile")
        }
    }
}

// MARK: - Color Extension
extension Color {
    init(hex: String) {
        let hex = hex.trimmingCharacters(in: CharacterSet.alphanumerics.inverted)
        var int: UInt64 = 0
        Scanner(string: hex).scanHexInt64(&int)
        let a, r, g, b: UInt64
        switch hex.count {
        case 6: (a, r, g, b) = (255, int >> 16, int >> 8 & 0xFF, int & 0xFF)
        default: (a, r, g, b) = (255, 0, 0, 0)
        }
        self.init(.sRGB, red: Double(r) / 255, green: Double(g) / 255, blue: Double(b) / 255, opacity: Double(a) / 255)
    }
}
