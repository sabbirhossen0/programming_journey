from django.db import models
from django.contrib.auth.models import User
from django.utils import timezone

class BlogPost(models.Model):
    STATE_CHOICES = [
        ('draft', 'Draft'),
        ('published', 'Published'),
    ]

    title = models.CharField(max_length=255)
    description = models.TextField()
    owner = models.ForeignKey(User, on_delete=models.CASCADE)
    state = models.CharField(max_length=10, choices=STATE_CHOICES, default='draft')
    read_count = models.IntegerField(default=0)
    reading_time = models.IntegerField(help_text="Estimated reading time in minutes")
    timestamp = models.DateTimeField(default=timezone.now)

    def __str__(self):
        return self.title
