from django.shortcuts import render
from JMapp.models import *
from django.http import HttpResponse
from django.shortcuts import get_object_or_404

# Create your views here.
def home(request):
    return render(request, 'home.html')

def SearchTrain(request):
    if request.method == 'GET':
        departure = request.GET.get('departure')
        destination = request.GET.get('destination')
        date = request.GET.get('date')

        if departure and destination and date:
            trains = Train_route.objects.filter(station__station_name=departure).filter(station__station_name=destination).filter(available_date=date)
            schedules = Schedule.objects.filter(start_station__station_name__iexact=departure).filter(end_station__station_name__iexact=destination)
            context = {
                'trains': trains,
                'schedules': schedules,
                'date': date
            }
            return render(request, 'search_results.html', context)
        return HttpResponse("Invalid input. Please fill all fields.")

def SeatPlan(request):
    if request.method == 'GET':
        selected_train = request.GET.get('selectTrain')
        date = request.GET.get('date')
        seats = Seat.objects.filter(train__train_name__iexact=selected_train, date=date)
        context = {
            'seats': seats
        }
        return render(request, 'available_seat.html', context)

def BuyTicket(request):
    if request.method=="POST":
        selected_seats=request.POST.getlist('selected_seats')
        context={
            'selected_seats':selected_seats
        }
        return render(request,'ticket_details.html',context)