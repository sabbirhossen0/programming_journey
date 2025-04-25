from .models import book, author
from rest_framework import serializers



class bookSerializer(serializers.ModelSerializer):
    class meta:
        model:book
        field=('name','author','price')
        def __str__(self):
            return self.name        
        

class authorSerializer(serializers.ModelSerializer):
    class meta:
        model:author
        field=('__all__')
        def  __str__(self):
            return self.name
