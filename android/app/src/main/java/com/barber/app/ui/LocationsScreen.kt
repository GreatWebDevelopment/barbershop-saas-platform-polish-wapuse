package com.barber.app.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.barber.app.data.Shop

val DarkBg = Color(0xFF0F0F1A)
val CardBg = Color(0xFF1A1A2E)
val Gold = Color(0xFFD4A853)
val LightText = Color(0xFFF5F0E8)

@Composable
fun LocationsScreen(
    onLocationClick: (Int) -> Unit,
    onCheckInClick: (Int) -> Unit
) {
    // Demo data - in production, fetch from API
    val locations = remember {
        listOf(
            Shop(1, "ClipMaster Downtown", "123 Main St", "Austin", "TX", "78701", "(512) 555-0001", 30.2672, -97.7431, waitingCount = 5, waitTime = 15),
            Shop(2, "ClipMaster Southside", "456 S Congress Ave", "Austin", "TX", "78704", "(512) 555-0002", 30.2460, -97.7489, waitingCount = 2, waitTime = 8),
            Shop(3, "ClipMaster North", "789 N Lamar Blvd", "Austin", "TX", "78756", "(512) 555-0003", 30.3160, -97.7530, waitingCount = 0, waitTime = 0),
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(DarkBg)
            .padding(16.dp)
    ) {
        Text(
            "Find a Location",
            fontSize = 28.sp,
            fontWeight = FontWeight.Bold,
            color = Color.White,
            modifier = Modifier.padding(bottom = 16.dp)
        )

        LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
            items(locations) { shop ->
                LocationCard(shop, onLocationClick, onCheckInClick)
            }
        }
    }
}

@Composable
fun LocationCard(
    shop: Shop,
    onLocationClick: (Int) -> Unit,
    onCheckInClick: (Int) -> Unit
) {
    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = CardBg),
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onLocationClick(shop.id) }
    ) {
        Column(modifier = Modifier.padding(20.dp)) {
            Text(shop.name, fontSize = 18.sp, fontWeight = FontWeight.Bold, color = Color.White)
            Text(shop.fullAddress, fontSize = 14.sp, color = Color.Gray, modifier = Modifier.padding(top = 4.dp))

            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 12.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(8.dp)
                            .clip(CircleShape)
                            .background(
                                when {
                                    (shop.waitingCount ?: 0) == 0 -> Color.Green
                                    (shop.waitingCount ?: 0) < 5 -> Color.Yellow
                                    else -> Color.Red
                                }
                            )
                    )
                    Text(
                        "${shop.waitingCount ?: 0} waiting",
                        fontSize = 13.sp,
                        color = Color.Gray,
                        modifier = Modifier.padding(start = 6.dp)
                    )
                }
                Text("~${shop.waitTime ?: 0} min wait", fontSize = 13.sp, color = Gold)
            }

            Button(
                onClick = { onCheckInClick(shop.id) },
                colors = ButtonDefaults.buttonColors(containerColor = Gold),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 16.dp)
            ) {
                Text("Check In Now", color = DarkBg, fontWeight = FontWeight.Bold, fontSize = 16.sp)
            }
        }
    }
}
