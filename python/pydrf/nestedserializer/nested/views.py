from django.shortcuts import render
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .models import book, author
from .serializers import bookSerializer,authorSerializer
# Create your views here.

@api_view(['GET'])
def home(request):
    return Response({'message':'hello world  sabbir hossen are you great man.'})


@api_view(['GET'])
def books(request):
    b=book.objects.all()
    serializer=bookSerializer(b,many=True)
    return Response(serializer.data,status=200)


@api_view(['GET'])
def authorview(request):
    b=author.objects.all()
    serializer=authorSerializer(b,many=True)
    return Response(serializer.data,status=200)