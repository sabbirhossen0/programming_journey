from django.contrib import admin

# Register your models here.
from .models import Subscription,Video

admin.site.register(Subscription)
admin.site.register(Video)
