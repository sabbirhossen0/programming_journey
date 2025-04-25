from django.db import models

# Create your models here.
class book(models.Model):
    name=models.CharField(max_length=100)
    author=models.CharField(max_length=100)
    price=models.IntegerField()
    
    def __str__(self):
        return self.name

class author (models.Model):
    name=models.CharField(max_length=100)
    age=models.IntegerField()
    # books=models.ManyToManyField(book)

    def __str__(self):
        return self.name