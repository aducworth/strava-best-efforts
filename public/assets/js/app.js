function formatDistance(distance,units) {
	if(units == 'feet') {
		return (convertMiles(distance)).toFixed(2) + ' mi'
	} else {
		if(distance < 1000) {
			return distance + ' m'
		} else {
			return (distance / 1000).toFixed(2) + ' km'
		}
	}
}

function convertMiles(distance) {
	return (distance * 0.000621371)
}

function convertMeters(distance) {
	return (distance * 1.609344 * 1000)
}

function formatDate(date) {
	var date = moment(date)
	return date.format('MM/DD/YY hh:mma')
}

function formatTime(time) {
	    
    var minutes = Math.floor( time / 60 )
    var seconds = ( time % 60 )
    var hours = Math.floor( minutes / 60 )
    minutes = ( minutes % 60 )
    
    return (hours?(hours+':'):'') + twoDigits(minutes,':') + twoDigits(seconds,'')
    
}

function twoDigits(value,followedBy) {
	if(value == 0) {
		return '00' + followedBy
	} else if(value <10) {
		return '0' + value + followedBy
	} else {
		return value + followedBy
	}
}

function formatTemperature(temp,units) {
	    
    if( temp == '' ) {
	    
	    return '';
	    
    }
    
    if( units == 'feet' ) {
	    		    
	    return ( Math.round( ( temp * (9/5) ) - 459.67 ) + 'F' );
	    
    }
    
    return ( Math.round( temp - 273.15 ) + 'C' );
    
} 


function calculatePace(meters,seconds,units) {
	    
    // if doing pace / mile
    if( units == 'feet' ) {
	    
	    var mile 				= 1609;
	    var mile_distance		= meters / mile;
	    var pace				= ( mile_distance > 0 )?Math.round( seconds / mile_distance ):0;
	    
	    return ( formatTime(pace) + ' / mi' );
	    
    }
    
    var kilometer 			= 1000;
    var km_distance			= meters / kilometer;
    var pace				= ( km_distance > 0 )?Math.round( seconds / km_distance ):0;
    
    return ( formatTime(pace) + ' / km' );
    
} 

function isUndefined(value){
    // Obtain `undefined` value that's
    // guaranteed to not have been re-assigned
    var undefined = void(0);
    return value === undefined;
}

function isToday(date) {
	console.log( 'date: ' + moment(date).format('MM/DD/YYYY') + ' today: ' + moment().format('MM/DD/YYYY'))
	return (moment().format('MM/DD/YYYY') == moment(date).format('MM/DD/YYYY'))?true:false
}
