/*=========================================================================================
    File Name: pricing.js
    Description: pricing page js
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(document).ready(function() {

    // variable for annual-plan and monthly plan container
    var annualyPlan = $('.annual-plan');
    var monthlyPlan = $('.monthly-plan');

    // variables for hights and maxheight and cardTopArea
    var cardTopArea, heightsArr = [], maxHeight, cardTopAreaWrapper; 

    // function for calcuation of top area height 
       function calcTopHeight() {
        // geting all the elements with the class of .card-top-area
        cardTopArea = $('.card-top-area');

        // getting all the elements with the class of .card-top-area-wrapper
        cardTopAreaWrapper = $('.card-top-area-wrapper');
        
        // making array empty each time while calling this function
        heightsArr = [];
        // looping through cardTopArea array to get the height of each element
        $.each(cardTopArea, function (indexInArray, eachElement) { 
            heightsArr.push(eachElement.offsetHeight);
        });

        // getting max height from heightsArr
        maxHeight = heightsArr.reduce(function(a, b) {
            return Math.max(a, b);
        });

        // applying max height to each element so that we could have top area properly aligned of each card
        $.each(cardTopAreaWrapper, function (indexInArray, eachElement) { 
            eachElement.style.height = maxHeight + 'px';
        });
       }

       calcTopHeight();

    //  click event on monthly button to switch plans
    $('#monthly').click(function() {
        for(var i = 0; i < monthlyPlan.length; i++) {
            if(monthlyPlan[i].classList.contains('hide')) {
                monthlyPlan[i].classList.remove('hide')
                annualyPlan[i].classList.add('hide')
            }
        }
        // callig calcTopHeight for top height calculation
        calcTopHeight()
    })
    
    // click event on annualy button to switch plans 
    $('#annualy').click(function() {
        for(var i = 0; i < monthlyPlan.length; i++) {
            if(annualyPlan[i].classList.contains('hide')) {
                annualyPlan[i].classList.remove('hide')
                monthlyPlan[i].classList.add('hide')
            }
        }

        // calling calcTopHeight for top height calculation
        calcTopHeight()
    })

});
