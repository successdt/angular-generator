var a = b
        .state('$fullState', {
          url: '/$url/',
          templateUrl: '$indexTplOutput',
          title: '$title',
          controller: '$indexControllerNameCtrl'
        })
        .state('$createState', {
          url: '/$url/tao-moi',
          templateUrl: '$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
        .state('$viewState', {
          url: '/$url/{id:[0-9]{1,8}}',
          templateUrl: '$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
