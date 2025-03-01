from django.contrib import admin

# Register your models here.
from .models import Subscription,User,Video

admin.site.register(Subscription)
admin.site.register(User)
admin.site.register(Video)
