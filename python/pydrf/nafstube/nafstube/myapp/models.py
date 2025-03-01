from django.db import models
from django.contrib.auth.models import User

class Video(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    title = models.CharField(max_length=255)
    description = models.TextField()
    video_file = models.FileField(upload_to='videos/')
    thumbnail = models.ImageField(upload_to='thumbnails/')
    views = models.IntegerField(default=0)
    upload_date = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.title

class Subscription(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='subscribers')
    subscribed_to = models.ForeignKey(User, on_delete=models.CASCADE, related_name='subscriptions')
    subscribed_at = models.DateTimeField(auto_now_add=True)
