var a = b
        .state('$fullState', {
          url: '/$url/',
          templateUrl: '$indexTplOutput',
          title: '$title',
          controller: '$indexControllerNameCtrl'
        })
        .state('$fullState.create', {
          url: '/tao-moi',
          templateUrl: '$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
        .state('$fullState.update', {
          url: '/{id:[0-9]{1,8}}',
          templateUrl: '$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
