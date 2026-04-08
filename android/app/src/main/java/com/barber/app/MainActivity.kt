package com.barber.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.barber.app.ui.*
import com.barber.app.ui.theme.BarberAppTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            BarberAppTheme {
                BarberNavigation()
            }
        }
    }
}

@Composable
fun BarberNavigation() {
    val navController = rememberNavController()

    NavHost(navController = navController, startDestination = "locations") {
        composable("locations") {
            LocationsScreen(
                onLocationClick = { shopId ->
                    navController.navigate("location/$shopId")
                },
                onCheckInClick = { shopId ->
                    navController.navigate("checkin/$shopId")
                }
            )
        }
        composable("location/{shopId}") { backStackEntry ->
            val shopId = backStackEntry.arguments?.getString("shopId")?.toIntOrNull() ?: return@composable
            LocationDetailScreen(
                shopId = shopId,
                onCheckIn = { navController.navigate("checkin/$shopId") },
                onBack = { navController.popBackStack() }
            )
        }
        composable("checkin/{shopId}") { backStackEntry ->
            val shopId = backStackEntry.arguments?.getString("shopId")?.toIntOrNull() ?: return@composable
            CheckInScreen(
                shopId = shopId,
                onSuccess = { queueNumber ->
                    navController.navigate("status/$queueNumber") {
                        popUpTo("locations")
                    }
                },
                onBack = { navController.popBackStack() }
            )
        }
        composable("status/{queueNumber}") { backStackEntry ->
            val queueNumber = backStackEntry.arguments?.getString("queueNumber") ?: return@composable
            QueueStatusScreen(
                queueNumber = queueNumber,
                onBack = { navController.navigate("locations") { popUpTo("locations") { inclusive = true } } }
            )
        }
    }
}
