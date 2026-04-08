package com.barber.app.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@Composable
fun QueueStatusScreen(
    queueNumber: String,
    onBack: () -> Unit
) {
    // In production: poll API for real-time status
    var status by remember { mutableStateOf("waiting") }
    var position by remember { mutableIntStateOf(3) }
    var waitMinutes by remember { mutableIntStateOf(12) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(DarkBg)
            .padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Text("Queue Number", color = Color.Gray, fontSize = 14.sp)
        Text(
            queueNumber,
            fontSize = 64.sp,
            fontWeight = FontWeight.Black,
            color = Gold
        )

        Spacer(modifier = Modifier.height(24.dp))

        // Status badge
        val (statusLabel, statusColor) = when (status) {
            "waiting" -> "In Line" to Color(0xFFFFA726)
            "called" -> "Your Turn!" to Color(0xFF66BB6A)
            "in_service" -> "In Service" to Color(0xFF42A5F5)
            "completed" -> "Done" to Color.Gray
            else -> status to Color.Gray
        }

        Surface(
            shape = RoundedCornerShape(20.dp),
            color = statusColor.copy(alpha = 0.15f)
        ) {
            Text(
                statusLabel,
                color = statusColor,
                fontWeight = FontWeight.Bold,
                fontSize = 20.sp,
                modifier = Modifier.padding(horizontal = 24.dp, vertical = 12.dp)
            )
        }

        if (status == "waiting") {
            Spacer(modifier = Modifier.height(32.dp))
            Row(horizontalArrangement = Arrangement.spacedBy(32.dp)) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text("#$position", fontSize = 36.sp, fontWeight = FontWeight.Bold, color = Color.White)
                    Text("Position", color = Color.Gray, fontSize = 14.sp)
                }
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text("~${waitMinutes}m", fontSize = 36.sp, fontWeight = FontWeight.Bold, color = Color.White)
                    Text("Est. Wait", color = Color.Gray, fontSize = 14.sp)
                }
            }
        }

        if (status == "called") {
            Spacer(modifier = Modifier.height(24.dp))
            Text(
                "Please head to the front desk!",
                color = Color.White,
                fontSize = 18.sp,
                textAlign = TextAlign.Center
            )
        }

        Spacer(modifier = Modifier.height(48.dp))

        if (status == "waiting" || status == "called") {
            OutlinedButton(
                onClick = { /* Cancel check-in */ onBack() },
                colors = ButtonDefaults.outlinedButtonColors(contentColor = Color.Red)
            ) {
                Text("Cancel Check-In")
            }
        }

        TextButton(onClick = onBack) {
            Text("Find Another Location", color = Color.Gray)
        }
    }
}
