from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name="home"),
    path('search_train/', views.SearchTrain, name="search_train"),
    path('available_seat/', views.SeatPlan, name='seatplan'),
    path('buy_tiicket',views.BuyTicket,name="buyticket"),
]