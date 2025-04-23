from django.db import models

# Create your models here.
class book(models.Model):
    name=models.CharField(max_length=100)
    author=models.CharField(max_length=100)
    price=models.IntegerField()
    

    def __str__(self):
        return self.name

c
