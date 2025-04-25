from django.contrib import admin
from .models import book, author

# Register your models here.
admin.site.register(book)
admin.site.register(author)
