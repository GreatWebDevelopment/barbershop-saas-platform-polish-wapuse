package com.barber.app.data

import retrofit2.http.*

interface ApiService {
    @GET("api/v1/locations/nearby")
    suspend fun getNearbyLocations(
        @Query("lat") lat: Double,
        @Query("lng") lng: Double,
        @Query("radius") radius: Double = 25.0
    ): LocationsResponse

    @GET("api/v1/locations/{shopId}")
    suspend fun getLocationDetail(@Path("shopId") shopId: Int): Map<String, Any>

    @POST("api/v1/queue/check-in")
    suspend fun checkIn(@Body request: CheckInRequest): CheckInResponse

    @GET("api/v1/queue/{queueNumber}")
    suspend fun getQueueStatus(@Path("queueNumber") queueNumber: String): QueueStatusResponse

    @DELETE("api/v1/queue/{queueNumber}")
    suspend fun cancelCheckIn(@Path("queueNumber") queueNumber: String)
}
