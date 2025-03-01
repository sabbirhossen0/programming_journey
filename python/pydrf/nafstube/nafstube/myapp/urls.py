from django.urls import path
from .views import VideoListView, VideoDetailView, SubscriptionView

urlpatterns = [
    path('', VideoListView.as_view(), name='video-list'),  # List all videos
    path('<int:pk>/', VideoDetailView.as_view(), name='video-detail'),  # Video details
    path('subscribe/', SubscriptionView.as_view(), name='subscribe'),  # Subscribe to a channel
]

