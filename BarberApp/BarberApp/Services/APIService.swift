import Foundation
import CoreLocation

class APIService: ObservableObject {
    static let shared = APIService()

    private let baseURL: String

    init(baseURL: String = "") {
        // Will be configured via environment or settings
        self.baseURL = baseURL.isEmpty
            ? (ProcessInfo.processInfo.environment["API_BASE_URL"] ?? "https://barbershop-saas-platform-polish.p.gwd.dev")
            : baseURL
    }

    func fetchNearbyLocations(lat: Double, lng: Double, radius: Double = 25) async throws -> [Shop] {
        let url = URL(string: "\(baseURL)/api/v1/locations/nearby?lat=\(lat)&lng=\(lng)&radius=\(radius)")!
        let (data, _) = try await URLSession.shared.data(from: url)
        let response = try JSONDecoder().decode(LocationsResponse.self, from: data)
        return response.locations
    }

    func checkIn(shopId: Int, name: String, phone: String, partySize: Int = 1, serviceId: Int? = nil, stylistPreference: String = "first_available") async throws -> CheckInResponse {
        let url = URL(string: "\(baseURL)/api/v1/queue/check-in")!
        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")

        var body: [String: Any] = [
            "shop_id": shopId,
            "customer_name": name,
            "customer_phone": phone,
            "party_size": partySize,
            "stylist_preference": stylistPreference,
        ]
        if let serviceId = serviceId {
            body["service_id"] = serviceId
        }

        request.httpBody = try JSONSerialization.data(withJSONObject: body)
        let (data, _) = try await URLSession.shared.data(for: request)
        return try JSONDecoder().decode(CheckInResponse.self, from: data)
    }

    func getQueueStatus(queueNumber: String) async throws -> QueueStatusResponse {
        let url = URL(string: "\(baseURL)/api/v1/queue/\(queueNumber)")!
        let (data, _) = try await URLSession.shared.data(from: url)
        return try JSONDecoder().decode(QueueStatusResponse.self, from: data)
    }

    func cancelCheckIn(queueNumber: String) async throws {
        let url = URL(string: "\(baseURL)/api/v1/queue/\(queueNumber)")!
        var request = URLRequest(url: url)
        request.httpMethod = "DELETE"
        _ = try await URLSession.shared.data(for: request)
    }
}
