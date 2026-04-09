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

// MARK: - Auth & User Models

struct AppUser: Codable, Identifiable {
    let id: Int
    let name: String
    let email: String
    let phoneNumber: String?
    let role: String?

    enum CodingKeys: String, CodingKey {
        case id, name, email, role
        case phoneNumber = "phone_number"
    }
}

struct AuthResponse: Codable {
    let user: AppUser
    let token: String
}

struct UserResponse: Codable {
    let user: AppUser
}

struct EmptyResponse: Codable {}

// MARK: - Appointment Models

struct Appointment: Codable, Identifiable {
    let id: Int
    let shopId: Int
    let staffId: Int
    let serviceId: Int
    let startsAt: String
    let endsAt: String
    let status: String
    let price: String?
    let staff: Staff?
    let service: Service?
    let shop: Shop?

    enum CodingKeys: String, CodingKey {
        case id, status, price, staff, service, shop
        case shopId = "shop_id"
        case staffId = "staff_id"
        case serviceId = "service_id"
        case startsAt = "starts_at"
        case endsAt = "ends_at"
    }

    var startsAtDate: Date? {
        let formatter = ISO8601DateFormatter()
        formatter.formatOptions = [.withInternetDateTime, .withFractionalSeconds]
        return formatter.date(from: startsAt) ?? {
            let f = DateFormatter()
            f.dateFormat = "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'"
            return f.date(from: startsAt)
        }()
    }
}

struct AppointmentsResponse: Codable {
    let appointments: [Appointment]
}

// MARK: - Service & Staff Detail Models

struct ServicesResponse: Codable {
    let services: [ServiceDetail]
}

struct ServiceDetail: Codable, Identifiable {
    let id: Int
    let name: String
    let description: String?
    let price: String
    let durationMinutes: Int
    let category: ServiceCategory?

    enum CodingKeys: String, CodingKey {
        case id, name, description, price, category
        case durationMinutes = "duration_minutes"
    }
}

struct ServiceCategory: Codable, Identifiable {
    let id: Int
    let name: String
}

struct StaffListResponse: Codable {
    let staff: [StaffMember]
}

struct StaffMember: Codable, Identifiable {
    let id: Int
    let name: String
    let title: String?
    let avatarUrl: String?
    let specialties: [String]?

    enum CodingKeys: String, CodingKey {
        case id, name, title, specialties
        case avatarUrl = "avatar_url"
    }
}

// MARK: - Booking Models

struct SlotsResponse: Codable {
    let slots: [String]
}

struct BookingResponse: Codable {
    let message: String
    let appointment: Appointment
}

// MARK: - Loyalty Models

struct LoyaltyResponse: Codable {
    let customers: [LoyaltyCustomer]
    let rewards: [LoyaltyReward]
    let totalPoints: Int

    enum CodingKeys: String, CodingKey {
        case customers, rewards
        case totalPoints = "total_points"
    }
}

struct LoyaltyCustomer: Codable, Identifiable {
    let id: Int
    let shopId: Int
    let loyaltyPoints: Int?
    let totalSpent: String?
    let firstName: String
    let lastName: String

    enum CodingKeys: String, CodingKey {
        case id
        case shopId = "shop_id"
        case loyaltyPoints = "loyalty_points"
        case totalSpent = "total_spent"
        case firstName = "first_name"
        case lastName = "last_name"
    }
}

struct LoyaltyReward: Codable, Identifiable {
    let id: Int
    let name: String?
    let description: String?
    let pointsCost: Int?

    enum CodingKeys: String, CodingKey {
        case id, name, description
        case pointsCost = "points_cost"
    }
}
