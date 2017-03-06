#!/bin/bash
echo "Downloading the latest app"

cd ../../storage/app
#ionic start ionic --v2

cd ../../../

# update apps
for APP in 'ion_blue_social' 'ion_car_market' 'ion_city_view' 'ion_dating_app' 'ion_fashion_shop' 'ionic2_social' 'ionic2_booking' 'ionic2_ecommerce' 'ionic2_food' 'ionic2_restaurant' 'ionic2_social' 'ionic2_taxi' 'ionic2_taxi_driver' 'ionic2_ui_kit'
#for APP in 'ionic2_social' 'ionic2_taxi' 'ionic2_taxi_driver' 'ionic2_ui_kit'
#for APP in 'ionic2_social'
do
    echo "Checking $APP"
    php angular-generator/artisan ionic:upgrade $APP
    cd $APP
    rm -rf node_modules
    npm install
    ionic serve
    #ionic upload
    echo "Files changed:"
    read answer
    git status
    echo -n "Commit the changed file (y/n)? "
    read answer
    if echo "$answer" | grep -iq "^y" ;then
        echo Yes
        git add --all
        git commit -m "upgrade to ionic 2.0.1"
        git push origin master
    else
        echo No
    fi
    cd ..
done 