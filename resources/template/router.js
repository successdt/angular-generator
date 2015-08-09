var a = b
        .state('$fullState', {
          url: '/$state/',
          templateUrl: 'tpl/$indexTplOutput',
          title: '$title',
          controller: '$indexControllerNameCtrl'
        })
        .state('$createState', {
          url: '/$state/tao-moi',
          templateUrl: 'tpl/$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
        .state('$viewState', {
          url: '/$state/{id:[0-9]{1,8}}',
          templateUrl: 'tpl/$itemTplOutput',
          title: '$title',
          controller: '$indexControllerNameCreateCtrl',
          activeState: '$fullState'
        })
