from django.urls import path
from .views import chat_with_gamini

urlpatterns = [
    path('chat/', chat_with_gamini),
]
