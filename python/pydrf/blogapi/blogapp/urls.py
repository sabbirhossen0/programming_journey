from django.urls import path
from .views import blog_post_list_create, blog_post_detail

urlpatterns = [
    path('posts/', blog_post_list_create, name='post-list'),  # List & Create
    path('posts/<int:pk>/', blog_post_detail, name='post-detail'),  # Retrieve, Update, Delete
]
