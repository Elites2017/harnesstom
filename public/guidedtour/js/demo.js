/* globals hopscotch: false */

/* ============ */
/* EXAMPLE TOUR */
/* ============ */
var tour = {
  id: 'hello-hopscotch',
  // i18n: {
  //   nextBtn: "NXT",
  //   prevBtn: "PRV",
  //   doneBtn: "DNE",
  //   skipBtn: "SKP"
  // },
  steps: [
    {
      target: 'hopscotch-title',
      title: 'Welcome to HARNESSTOM Database!',
      content: 
              "HarnesstomDB is a comprehensive publicly \
              available open-source multi-omics database \
              for facilitating the use of the potential \
              of tomato germplasm collections for breeding. \
              It aims to store curated data from previously EU-funded project and other tomato related data",
      placement: 'bottom',
      onShow: function() {
        $('#startTourBtn').hide();
      },
      arrowOffset: 60
    },
    {
      target: 'nav-menu',
      title: 'HarnesstomDB Modules!',
      content: 
              'This is the navigation menu in which the modules \
               are presented. They can be accessed to use \
               the functionality of the system.',
      placement: 'bottom',
      arrowOffset: 60,
      onShow: function() {
        $('#phendata').hover();
      }
    },
    {
      target: 'db-provided-data',
      title: 'Database summary!',
      content: 'This section gives a brief idea of the quantity of data stored in the DB by module, contry...',
      placement: 'top',
      arrowOffset: 60,
    },
    {
      target: document.querySelectorAll('#general-use-desc code')[1],
      title: 'Where to begin',
      content: 'At the very least, you\'ll need to include these two files in your project to get started.',
      placement: 'right',
      yOffset: -20
    },
    {
      target: 'my-first-tour-file',
      placement: 'top',
      title: 'Define and start your tour',
      content: 'Once you have Hopscotch on your page, you\'re ready to start making your tour! The biggest part of your tour definition will probably be the tour steps.'
    },
    {
      target: 'main-sources',
      title: 'Main sources!',
      content: 'Data have been collected from previous EU-funded project. Some of them are listed here.',
      placement: 'top',
      arrowOffset: 60
    },
    {
      target: 'funding',
      title: 'HARNESSTOM Project Funding!',
      content: 'The HARNESSTOM project has received funding from the European Union’s \
                Horizon 2020 research and innovation programme under grant agreement No 101000716.',
      placement: 'top',
      arrowOffset: 60,
    },
    {
      target: 'dev-team',
      title: 'Dev Team!',
      content: 'This platform has created by the INPT through LRSV in collaboration with UPV',
      placement: 'top',
      arrowOffset: 60,
    },
    {
      target: 'start-tour',
      placement: 'right',
      title: 'Starting your tour',
      content: 'After you\'ve created your tour, pass it in to the startTour() method to start it.',
      yOffset: -25
    },
    {
      target: 'basic-options',
      placement: 'left',
      title: 'Basic step options',
      content: 'These are the most basic step options: <b>target</b>, <b>title</b>, <b>content</b>, and <b>placement</b>. For some steps, they may be all you need.',
      arrowOffset: 100,
      yOffset: -80
    },
    {
      target: 'api-methods',
      placement: 'top',
      title: 'Hopscotch API methods',
      content: 'Control your tour programmatically using these methods.',
    },
    {
      target: 'tour-example',
      placement: 'top',
      title: 'This tour\'s code',
      content: 'This is the JSON for the current tour! Pretty simple, right?',
    },
    {
      nextOnTargetClick: true,
      multipage: true,
      target: 'login-btn',
      title: 'Login Button',
      content: 
              'This is a button that will bring you to the login page. \
               Certain functionalities can be accessed only by logged users. \
               On the login page you can connect using your credentials or \
               you can create an account. Click the button',
      placement: 'bottom',
      arrowOffset: 260,
      xOffset: -260
    },
    {
      target: 'sign-in',
      title: 'Sign In!',
      content: 'If you already have an account, please sign in with your account credentials otherwise you can use the sign up link, by clicking next ',
      placement: 'top',
      arrowOffset: 60,
    },
    {
      nextOnTargetClick: true,
      multipage: true,
      target: 'sign-up',
      title: 'Sign up link!',
      content: 'If you want to sign up / register, click on the link above.',
      placement: 'bottom',
      arrowOffset: 60,
    },
    {
      nextOnTargetClick: true,
      multipage: true,
      target: 'sign-up-page',
      title: 'Registration!',
      content: 'On this page, you can create an account by filling the mandatory fields. You have two sections, one for personnal data & another for credentials data.',
      placement: 'top',
      arrowOffset: 60,
    },
    {
      nextOnTargetClick: true,
      multipage: true,
      target: 'register-btn',
      title: 'Registration button!',
      content: 'Once you have filled all the required fields, click on this button to register / create your account. <b> Note after the registration, you will receive an email to confirm your email address. </b>',
      placement: 'bottom',
      arrowOffset: 60,
    },
    {
      target: 'hopscotch-title',
      placement: 'bottom',
      title: 'You\'re all set!',
      content: 'Now go and use HARNESSTOM DB!',
      onShow: function() {
        $('#startTourBtn').show();
      },
    }
  ],
  showPrevButton: true,
  scrollTopMargin: 100
},

/* ========== */
/* TOUR SETUP */
/* ========== */
addClickListener = function(el, fn) {
  if (el.addEventListener) {
    el.addEventListener('click', fn, false);
  }
  else {
    el.attachEvent('onclick', fn);
  }
},

init = function() {
  var startBtnId = 'startTourBtn',
      calloutId = 'startTourCallout',
      mgr = hopscotch.getCalloutManager(),
      state = hopscotch.getState();

  if (state && state.indexOf('hello-hopscotch:') === 0) {
    // Already started the tour at some point!
    hopscotch.startTour(tour);
  }
  else {
    // Looking at the page for the first(?) time.
    setTimeout(function() {
      mgr.createCallout({
        id: calloutId,
        target: startBtnId,
        placement: 'right',
        title: 'Take an example tour',
        content: 'Start by taking an example tour to see some of the most functionalities of HARNESSTOM DB!',
        yOffset: -25,
        arrowOffset: 20,
        width: 240
      });
    }, 100);
  }

  addClickListener(document.getElementById(startBtnId), function() {
    if (!hopscotch.isActive) {
      mgr.removeAllCallouts();
      hopscotch.startTour(tour);
    }
  });
};

init();

