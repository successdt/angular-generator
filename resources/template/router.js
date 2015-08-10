var a = b
        .state('$fullState', {
          url: '/$state/',
          templateUrl: '$indexTplOutput',
          title: '$title',
          controller: '$indexControllerNameCtrl'
        })
        .state('$createState', {
          url: '/$state/tao-moi',
          templateUrl: '$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
        .state('$viewState', {
          url: '/$state/{id:[0-9]{1,8}}',
          templateUrl: '$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
