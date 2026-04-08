import SwiftUI

@main
struct BarberApp: App {
    @StateObject private var appState = AppState()

    var body: some Scene {
        WindowGroup {
            ContentView()
                .environmentObject(appState)
        }
    }
}

class AppState: ObservableObject {
    @Published var isLoggedIn = false
    @Published var currentQueueEntry: QueueEntry?
    @Published var selectedShop: Shop?
}
