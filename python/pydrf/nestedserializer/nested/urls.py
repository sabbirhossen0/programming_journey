from django.urls import path 
from .views import home,books

urlpatterns=[
    path('home/',home),
    path('books/',books)
]