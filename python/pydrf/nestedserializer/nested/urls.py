from django.urls import path 
from .views import home,books,authorview

urlpatterns=[
    path('home/',home),
    path('books/',books),
    path('author/',authorview)

]