from django.contrib import admin
from .models import Train, Station,Fare,Seat,Schedule,Train_route
# Register your models here.
admin.site.register(Train)
admin.site.register(Station)
admin.site.register(Train_route)
class FareAdmin(admin.ModelAdmin):
    list_display=('train','start_station','end_station','fare_amount',)

admin.site.register(Fare,FareAdmin)

class SeatAdmin(admin.ModelAdmin):
    list_display = ('train', 'date', 'seat_number', 'is_available')
    list_filter = ('date',)
    list_editable=('is_available',)

admin.site.register(Seat, SeatAdmin)

class ScheduleAdmin(admin.ModelAdmin):
    list_display=('train','start_station','start_time','end_station','arrival_time')
   

admin.site.register(Schedule,ScheduleAdmin)
