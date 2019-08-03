import Vue from 'vue'
import Router from 'vue-router'
import Orders from '@/components/Orders'
import ExpZabor from '@/components/ExpZabor'
import ExpDostavka from '@/components/ExpDostavka'
import ExpRaschet from '@/components/ExpRaschet'
import ExpZaborArchive from '@/components/ExpZaborArchive'
import ExpDostavkaArchive from '@/components/ExpDostavkaArchive'
import Sklads from '@/components/Sklads'
import Perevozka from '@/components/Perevozka'
import Reis from '@/components/Reis'
import ReisArchive from '@/components/ReisArchive'
import Transit from '@/components/Transit'
import SkladKrim from '@/components/SkladKrim'
import Dostavka from '@/components/Dostavka'
import Neoplata from '@/components/Neoplata'
import Archive from '@/components/Archive'
import Lists from '@/components/Lists'
import Reports from '@/components/Reports'
import ArchiveTotal from '@/components/ArchiveTotal'
import Settings from '@/components/Settings'

import Uric from '@/components/lists/Uric'
import Fisic from '@/components/lists/Fisic'
import Contractor from '@/components/lists/Contractor'
import User from '@/components/lists/User'
import Sklad from '@/components/lists/Sklad'
import AOUric from '@/components/lists/AOUric'
import AOFisic from '@/components/lists/AOFisic'
import Driver from '@/components/lists/Driver'
import Car from '@/components/lists/Car'
import Dispetcher from '@/components/lists/Dispetcher'

import ManagerOrders from '@/components/reports/ManagerOrders'
import RepPerevozka from '@/components/reports/RepPerevozka'
import RepReis from '@/components/reports/RepReis'
import RepReisDetail from '@/components/reports/RepReisDetail'
import RepClientOrders from '@/components/reports/RepClientOrders'
import RepExps from '@/components/reports/RepExps'
import RepExpDetail from '@/components/reports/RepExpDetail'

Vue.use(Router)

let routes_list = [
    {
      path: 'uric',
      name: 'listsUric',
      component: Uric
    },
    {
      path: 'fisic',
      name: 'listsFisic',
      component: Fisic
    },
    {
      path: 'cont',
      name: 'listsContractor',
      component: Contractor
    },
    {
      path: 'user',
      name: 'listsUser',
      component: User
    },
    {
      path: 'sklad',
      name: 'listsSklad',
      component: Sklad
    },
    {
      path: 'aouric',
      name: 'listsAOUric',
      component: AOUric
    },
    {
      path: 'aofisic',
      name: 'listsAOFisic',
      component: AOFisic
    },
    {
      path: 'driver',
      name: 'listsDriver',
      component: Driver
    },
    {
      path: 'car',
      name: 'listsCar',
      component: Car
    },
    {
      path: 'disp',
      name: 'listsDispetcher',
      component: Dispetcher
    },
];

let routes_reports = [
    {
      path: 'manorders',
      name: 'RepManagerOrders',
      component: ManagerOrders
    },
    {
      path: 'repperevozka',
      name: 'RepPerevozka',
      component: RepPerevozka
    },
    {
      path: 'repreis',
      name: 'RepReis',
      component: RepReis
    },
    {
      path: 'repreisdetail',
      name: 'RepReisDetail',
      component: RepReisDetail
    },
    {
      path: 'repclientorders',
      name: 'RepClientOrders',
      component: RepClientOrders
    },
    {
      path: 'repexps',
      name: 'RepExps',
      component: RepExps
    },
    {
      path: 'repexpdetail',
      name: 'RepExpDetail',
      component: RepExpDetail
    },
];

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Orders',
      component: Orders
    },
    {
      path: '/expzabor',
      name: 'ExpZabor',
      component: ExpZabor
    },
    {
      path: '/expdost',
      name: 'ExpDostavka',
      component: ExpDostavka
    },
    {
      path: '/raszabor',
      name: 'ExpRaschetZabor',
      component: ExpRaschet,
      props: { exp_type: '1' }
    },
    {
      path: '/rasdost',
      name: 'ExpRaschetDostavka',
      component: ExpRaschet,
      props: { exp_type: '2' }
    },
    {
      path: '/archzab',
      name: 'ExpZaborArchive',
      component: ExpZaborArchive
    },
    {
      path: '/archdost',
      name: 'ExpDostavkaArchive',
      component: ExpDostavkaArchive
    },
    {
      path: '/skladmsk',
      name: 'Sklads',
      component: Sklads
    },
    {
      path: '/perevozka',
      name: 'Perevozka',
      component: Perevozka
    },
    {
      path: '/reis',
      name: 'Reis',
      component: Reis
    },
    {
      path: '/archreis',
      name: 'ReisArchive',
      component: ReisArchive
    },
    {
      path: '/transit',
      name: 'Transit',
      component: Transit
    },
    {
      path: '/skladkrim',
      name: 'SkladKrim',
      component: SkladKrim
    },
    {
      path: '/dost',
      name: 'Dostavka',
      component: Dostavka
    },
    {
      path: '/neoplata',
      name: 'Neoplata',
      component: Neoplata
    },
    {
      path: '/dones',
      name: 'Archive',
      component: Archive
    },
    {
      path: '/lists',
      name: 'Lists',
      component: Lists,
      children: routes_list
    },
    {
      path: '/reports',
      name: 'Reports',
      component: Reports,
      children: routes_reports
    },
    {
      path: '/archtotal',
      name: 'ArchiveTotal',
      component: ArchiveTotal
    },
    {
      path: '/settings',
      name: 'Settings',
      component: Settings
    }
  ]
})
