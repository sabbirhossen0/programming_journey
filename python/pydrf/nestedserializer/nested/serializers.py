from rest_framework import serializers
from .models import book, author




class bookSerializer(serializers.ModelSerializer):
    class Meta:
        model = book  # This line is REQUIRED
        fields = '__all__'      

# class authorSerializer(serializers.ModelSerializer):
#     class Meta:
#         model = author  # This line is REQUIRED
#         fields = '__all__'          

class authorSerializer(serializers.ModelSerializer):
    class Meta:
        model = author
        fields= '__all__'
