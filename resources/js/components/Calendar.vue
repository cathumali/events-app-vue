<template>
	<div>
		<h2>Calendar</h2>
            <div class="row">
                <div class="col-5">
                	<form >
						<div class="form-group">
							<input type="text" class="form-control" required aria-label="Small" aria-describedby="inputGroup-sizing-sm" v-model="event.name">
						</div>
						<div class="form-group">
							<date-range-picker v-model="range" required class="form-control" id="datepicker" :options="options" @change="setRangeValue"/>
						</div>

						<div class="form-group required">
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="monday" :value="checkedDays.mon" v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="monday">Mon</label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="tue" :value="checkedDays.tue" v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="tue">Tue</label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="wed" :value="checkedDays.wed" v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="wed">Wed</label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="thur" :value="checkedDays.thur" v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="thur">Thur</label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="fri" :value="checkedDays.fri"  v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="fri">Fri</label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="sat" :value="checkedDays.sat" v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="sat">Sat</label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="checkbox" id="sun" :value="checkedDays.sun" v-model="checkedDays_" @click="handleCheck" >
							  <label class="form-check-label" for="sun">Sun</label>
							</div>				
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary" @click="handleSubmit">Save</button>
						</div>
					</form>
                </div>
                <div class="col-7">
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col"><h3>{{this.startDate}}</h3></th>
                          <th scope="col"></th> 
                        </tr>
                      </thead>
                      <tbody >
                        <tr v-for="event in allEvents"  v-bind:key="event.id" >
                          <td scope="row"><b>{{ event.date }} - </b>    {{ event.name }}</td>
                          <td></td> 
                        </tr>
                      </tbody>
                    </table>
                </div>
            </div>
	</div>
</template>

<script>
 import moment from 'moment'

  export default {

	data () {
		return {
		  range: '',
		  options: {
		      locale: {
		        format: 'DD/MM/YYYY',
		      }
		  },
		  event: {
		  	name: '',
		  	date: ''
		  },
		  checkedDays_ : [],
		  checkedDays: {
		  	mon : 'mon',
		  	tue : 'tue',
		  	wed : 'wed',
		  	thur : 'thur',
		  	fri : 'fri',
		  	sat : 'sat',
		  	sun : 'sun',		  	
		  },
		  edit: false,
		  startDate: '',
		  dateranges:null,
		  formData:null ,
		  allEvents: null
		};
	},
	 created() {
		this.fetchEvents();
		this.getDateToday();
	  },

	methods: {

	    fetchEvents() {
	    	let events = null;
	      	fetch('events')
	        .then(res => res.json())
	        .then(res => {
	          events = res;
	          this.allEvents = this.processEvents(res) ;
	          console.log(this.processEvents(res));
	        })
	        .catch(err => console.log(err));
	        
	    },
	    processEvents(events) {

			let processEvents = [];
			var first_iteration = true;
			for (var key in events) {
				
				var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", 
				"November", "December"];
				 
				let days = ["mon", "tue", "wed", "thur", "fri", "sat", "sun"];
				let d = new Date(key);
				let date = d.getDate()+' '+days[d.getDay()];
				if(first_iteration){
					this.startDate = months[d.getMonth()]+' '+d.getDate()+', '+d.getFullYear();
					first_iteration = false;
				}
				

				processEvents.push({
					name: (events[key] !== null ) ? events[key][0].event_name : '',
					date: date,
					original_date: d
				});

			}
			return processEvents;					
	    },
	
      	addEvent(){
			  console.log(this.formData);
	      if (this.edit === false) {
	        fetch('/events', {
	          method: 'post',
	          body: JSON.stringify(this.formData),
	          headers: {
	            'content-type': 'application/json',
	            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
	          }
	        })
	          .then(res => res.json())
	          .then(data => {
	            this.fetchEvents();
	          })
	          .catch(err => { console.log(err) });
	      } 
      },
      setRangeValue(value){

		if(value) {
      		let newdates = [];
	      	value.forEach(item => {
				let datearray = item.split("/");
				let newdate = datearray[1] + '/' + datearray[0] + '/' + datearray[2];
				newdates.push(newdate);
				
	      	});

	      	var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", 
	      	"November", "December"];
	      	let d= new Date(newdates[0]);
	      	this.startDate = months[d.getMonth()]+' '+d.getDate()+', '+d.getFullYear();

	      	let dateranges = this.getDates(newdates[0], newdates[1]) ;
	      	this.dateranges = dateranges;
			console.log(dateranges);
      	}
      },

	getDates(startDate, stopDate) {
	    var dateArray = [];
	    var currentDate = moment(startDate);
	    var stopDate = moment(stopDate);
	    while (currentDate <= stopDate) {
	        dateArray.push( moment(currentDate).format('YYYY-MM-DD') )
	        currentDate = moment(currentDate).add(1, 'days');
	    }
	    return dateArray;
	},
      handleCheck(value){
		  this.checkedDays_.push(value.target.value);
		  console.log(this.checkedDays_);
      },		
      handleSubmit(value){
      	value.preventDefault();
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		if(checkedOne === true ) {
			var days = ["mon", "tue", "wed", "thur", "fri", "sat", "sun"];
			let data = [];
			
			if(this.dateranges == null ) {
				this.setRangeValue(document.getElementById("datepicker").value.replace(/\s/g, '').split('-'));
			}
			console.log(this.dateranges);
			this.dateranges.forEach(item => {
			
				let getday = days[new Date(item).getDay()];
				let value = {	
					name: this.event.name,
					date: item
				}

				if(this.checkedDays_.includes(getday)) {
					data.push(value);
				}
			});
			this.formData = data;
			this.addEvent();
		}else{
			alert('Please choose atleast one day!');
		}    

	  },

	  
	  getDateToday(){
		  let today = new Date();
		  let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", 
	      	"November", "December"];
		  this.startDate = months[today.getMonth()]+' '+today.getDate()+', '+today.getFullYear();
	  }
	}
  };
</script>