import SwiftUI

@main
struct BarberApp: App {
    @StateObject private var auth = AuthManager.shared
    @StateObject private var appState = AppState()

    var body: some Scene {
        WindowGroup {
            MainTabView()
                .environmentObject(auth)
                .environmentObject(appState)
        }
    }
}

class AppState: ObservableObject {
    @Published var currentQueueEntry: QueueEntry?
    @Published var selectedShop: Shop?
}
