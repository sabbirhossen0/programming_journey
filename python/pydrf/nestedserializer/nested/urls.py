from django.urls import path 
from .views import home,books,authorview,bookcreate,authorcreate,booksearch

urlpatterns=[
    
    path('home/',home),
    path('books/',books),
    path('author/',authorview),
    path('addbook/',bookcreate),
    path('addauthor/',authorcreate),
    path('search/',booksearch,name='search book')

]