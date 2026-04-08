import SwiftUI

@main
struct BarberKioskApp: App {
    @State private var shopId: Int = 1

    var body: some Scene {
        WindowGroup {
            KioskCheckInView(shopId: shopId)
        }
    }
}
