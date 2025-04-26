from django.urls import path 
from .views import home,books,authorview,bookcreate,authorcreate,booksearch,bookdetails

urlpatterns=[

    path('home/',home),
    path('books/',books),
    path('author/',authorview),
    path('addbook/',bookcreate),
    path('addauthor/',authorcreate),
    path('search/',booksearch,name='search book'),
    path('bookdetails/<int:pk>/',bookdetails,name='search book by id ')

]