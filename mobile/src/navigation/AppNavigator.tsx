import React from 'react';
import {createStackNavigator} from '@react-navigation/stack';
import {createBottomTabNavigator} from '@react-navigation/bottom-tabs';
import {useAppSelector} from '../store/hooks';
import LoginScreen from '../screens/LoginScreen';
import HomeScreen from '../screens/HomeScreen';
import ScannerScreen from '../screens/ScannerScreen';
import ProductDetailScreen from '../screens/ProductDetailScreen';
import MovimientoScreen from '../screens/MovimientoScreen';
import RutasScreen from '../screens/RutasScreen';
import RutaDetailScreen from '../screens/RutaDetailScreen';

const Stack = createStackNavigator();
const Tab = createBottomTabNavigator();

function HomeStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="HomeMain" component={HomeScreen} options={{title: 'Inicio'}} />
      <Stack.Screen name="ProductDetail" component={ProductDetailScreen} options={{title: 'Producto'}} />
      <Stack.Screen name="Movimiento" component={MovimientoScreen} options={{title: 'Movimiento'}} />
    </Stack.Navigator>
  );
}

function ScannerStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="ScannerMain" component={ScannerScreen} options={{title: 'Escáner'}} />
      <Stack.Screen name="ProductDetail" component={ProductDetailScreen} options={{title: 'Producto'}} />
      <Stack.Screen name="Movimiento" component={MovimientoScreen} options={{title: 'Movimiento'}} />
    </Stack.Navigator>
  );
}

function RutasStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="RutasMain" component={RutasScreen} options={{title: 'Rutas'}} />
      <Stack.Screen name="RutaDetail" component={RutaDetailScreen} options={{title: 'Detalle Ruta'}} />
    </Stack.Navigator>
  );
}

function MainTabs() {
  return (
    <Tab.Navigator screenOptions={{headerShown: false}}>
      <Tab.Screen name="Home" component={HomeStack} options={{tabBarLabel: 'Inicio'}} />
      <Tab.Screen name="Scanner" component={ScannerStack} options={{tabBarLabel: 'Escáner'}} />
      <Tab.Screen name="Rutas" component={RutasStack} options={{tabBarLabel: 'Rutas'}} />
    </Tab.Navigator>
  );
}

function AuthStack() {
  return (
    <Stack.Navigator screenOptions={{headerShown: false}}>
      <Stack.Screen name="Login" component={LoginScreen} />
    </Stack.Navigator>
  );
}

export default function AppNavigator() {
  const token = useAppSelector(s => s.auth.token);

  return token ? <MainTabs /> : <AuthStack />;
}
