package com.barber.app.data

import com.google.gson.annotations.SerializedName

data class Shop(
    val id: Int,
    val name: String,
    val address: String? = null,
    val city: String? = null,
    val state: String? = null,
    val zip: String? = null,
    val phone: String? = null,
    val latitude: Double? = null,
    val longitude: Double? = null,
    val hours: Map<String, String>? = null,
    @SerializedName("queue_enabled") val queueEnabled: Boolean? = null,
    @SerializedName("waiting_count") val waitingCount: Int? = null,
    @SerializedName("wait_time") val waitTime: Int? = null,
    val distance: Double? = null
) {
    val fullAddress: String
        get() = listOfNotNull(address, city, state, zip).joinToString(", ")
}

data class QueueEntry(
    val id: Int,
    @SerializedName("queue_number") val queueNumber: String,
    @SerializedName("customer_name") val customerName: String,
    @SerializedName("customer_phone") val customerPhone: String,
    @SerializedName("party_size") val partySize: Int,
    val status: String,
    val position: Int,
    @SerializedName("estimated_wait_minutes") val estimatedWaitMinutes: Int,
    val shop: Shop? = null,
    val staff: Staff? = null
)

data class Staff(
    val id: Int,
    val name: String,
    val title: String? = null,
    @SerializedName("avatar_url") val avatarUrl: String? = null
)

data class Service(
    val id: Int,
    val name: String,
    val price: Double,
    @SerializedName("duration_minutes") val durationMinutes: Int
)

data class CheckInRequest(
    @SerializedName("shop_id") val shopId: Int,
    @SerializedName("customer_name") val customerName: String,
    @SerializedName("customer_phone") val customerPhone: String,
    @SerializedName("party_size") val partySize: Int = 1,
    @SerializedName("stylist_preference") val stylistPreference: String = "first_available"
)

data class CheckInResponse(
    val message: String,
    @SerializedName("queue_entry") val queueEntry: QueueEntry,
    @SerializedName("queue_number") val queueNumber: String,
    val position: Int,
    @SerializedName("estimated_wait_minutes") val estimatedWaitMinutes: Int
)

data class LocationsResponse(
    val locations: List<Shop>
)

data class QueueStatusResponse(
    @SerializedName("queue_entry") val queueEntry: QueueEntry,
    @SerializedName("people_ahead") val peopleAhead: Int,
    @SerializedName("estimated_wait_minutes") val estimatedWaitMinutes: Int
)
