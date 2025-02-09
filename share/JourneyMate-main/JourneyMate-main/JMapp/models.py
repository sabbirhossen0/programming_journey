from django.db import models

# Create your models here.
class Train(models.Model):
    train_name=models.CharField(max_length=100, unique=True)
    train_number=models.IntegerField(unique=True)

    def __str__(self):
        return self.train_name
    
class Station(models.Model):
    station_name=models.CharField(max_length=100,unique=True)
    distance_from_start=models.IntegerField()

    def __str__(self):
        return self.station_name
    
class Fare(models.Model):
    train = models.ForeignKey(Train, on_delete=models.CASCADE)
    start_station = models.ForeignKey(Station, related_name='start_station', on_delete=models.CASCADE)
    end_station = models.ForeignKey(Station, related_name='end_station', on_delete=models.CASCADE)
    fare_amount = models.DecimalField(max_digits=10, decimal_places=2)

    def __str__(self):
        return f"{self.train.train_name} - {self.start_station.station_name} to {self.end_station.station_name} - {self.fare_amount}"

class Seat(models.Model):
    train = models.ForeignKey(Train, on_delete=models.CASCADE)
    date = models.DateField()
    seat_number = models.CharField(max_length=10)
    is_available = models.BooleanField(default=True)

    def __str__(self):
        return f"{self.train.train_name} - {self.date} - Seat {self.seat_number} - {'Available' if self.is_available else 'Not Available'}"

class Schedule(models.Model):
    train = models.ForeignKey(Train, on_delete=models.CASCADE)
    start_station = models.ForeignKey(Station, related_name='start', on_delete=models.CASCADE)
    end_station = models.ForeignKey(Station, related_name='end', on_delete=models.CASCADE)
    start_time = models.TimeField()
    arrival_time = models.TimeField()

    def __str__(self):
        return self.train.train_name
    
class Train_route(models.Model):
    Train_name=models.ForeignKey(Train,on_delete=models.CASCADE)
    station=models.ManyToManyField(Station)
    available_date=models.DateField()

    def __str__(self):
        return self.Train_name.train_name
    