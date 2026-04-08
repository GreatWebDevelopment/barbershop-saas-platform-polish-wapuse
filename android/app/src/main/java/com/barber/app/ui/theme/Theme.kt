package com.barber.app.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

private val DarkColorScheme = darkColorScheme(
    primary = Color(0xFFD4A853),
    onPrimary = Color(0xFF0F0F1A),
    secondary = Color(0xFF1A1A2E),
    background = Color(0xFF0F0F1A),
    surface = Color(0xFF1A1A2E),
    onBackground = Color(0xFFF5F0E8),
    onSurface = Color(0xFFF5F0E8),
)

@Composable
fun BarberAppTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = DarkColorScheme,
        content = content
    )
}
