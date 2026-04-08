import Foundation
import CoreLocation

struct Shop: Codable, Identifiable {
    let id: Int
    let name: String
    let address: String?
    let city: String?
    let state: String?
    let zip: String?
    let phone: String?
    let latitude: Double?
    let longitude: Double?
    let hours: [String: String]?
    let queueEnabled: Bool?
    let waitingCount: Int?
    let waitTime: Int?

    var coordinate: CLLocationCoordinate2D? {
        guard let lat = latitude, let lng = longitude else { return nil }
        return CLLocationCoordinate2D(latitude: lat, longitude: lng)
    }

    var fullAddress: String {
        [address, city, state, zip].compactMap { $0 }.joined(separator: ", ")
    }

    enum CodingKeys: String, CodingKey {
        case id, name, address, city, state, zip, phone, latitude, longitude, hours
        case queueEnabled = "queue_enabled"
        case waitingCount = "waiting_count"
        case waitTime = "wait_time"
    }
}

struct QueueEntry: Codable, Identifiable {
    let id: Int
    let queueNumber: String
    let customerName: String
    let customerPhone: String
    let partySize: Int
    let status: String
    let position: Int
    let estimatedWaitMinutes: Int
    let shop: Shop?
    let staff: Staff?

    enum CodingKeys: String, CodingKey {
        case id, status, position, shop, staff
        case queueNumber = "queue_number"
        case customerName = "customer_name"
        case customerPhone = "customer_phone"
        case partySize = "party_size"
        case estimatedWaitMinutes = "estimated_wait_minutes"
    }
}

struct Staff: Codable, Identifiable {
    let id: Int
    let name: String
    let title: String?
    let avatarUrl: String?

    enum CodingKeys: String, CodingKey {
        case id, name, title
        case avatarUrl = "avatar_url"
    }
}

struct Service: Codable, Identifiable {
    let id: Int
    let name: String
    let price: Double
    let durationMinutes: Int

    enum CodingKeys: String, CodingKey {
        case id, name, price
        case durationMinutes = "duration_minutes"
    }
}

struct CheckInResponse: Codable {
    let message: String
    let queueEntry: QueueEntry
    let queueNumber: String
    let position: Int
    let estimatedWaitMinutes: Int

    enum CodingKeys: String, CodingKey {
        case message, position
        case queueEntry = "queue_entry"
        case queueNumber = "queue_number"
        case estimatedWaitMinutes = "estimated_wait_minutes"
    }
}

struct LocationsResponse: Codable {
    let locations: [Shop]
}

struct QueueStatusResponse: Codable {
    let queueEntry: QueueEntry
    let peopleAhead: Int
    let estimatedWaitMinutes: Int

    enum CodingKeys: String, CodingKey {
        case queueEntry = "queue_entry"
        case peopleAhead = "people_ahead"
        case estimatedWaitMinutes = "estimated_wait_minutes"
    }
}
