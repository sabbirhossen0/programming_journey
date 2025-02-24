from django.contrib.auth.models import User
from rest_framework.response import Response
from rest_framework.decorators import api_view
from rest_framework_simplejwt.tokens import RefreshToken
from django.contrib.auth import authenticate

# Generate tokens for user
def get_tokens_for_user(user):
    refresh = RefreshToken.for_user(user)
    return {
        "refresh": str(refresh),
        "access": str(refresh.access_token),
    }

# Register User
@api_view(["POST"])
def register(request):
    username = request.data.get("username")
    email = request.data.get("email")
    password = request.data.get("password")

    if User.objects.filter(username=username).exists():
        return Response({"error": "Username already taken"}, status=400)

    user = User.objects.create_user(username=username, email=email, password=password)
    return Response({"message": "User created successfully"})

# Login User
@api_view(["POST"])
def login(request):
    username = request.data.get("username")
    password = request.data.get("password")

    user = authenticate(username=username, password=password)
    if user:
        tokens = get_tokens_for_user(user)
        return Response(tokens)
    return Response({"error": "Invalid Credentials"}, status=400)

# Protected API (Test)
@api_view(["GET"])
def profile(request):
    return Response({"message": "This is a protected route", "user": request.user.username})
