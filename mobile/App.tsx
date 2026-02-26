import React, {useEffect} from 'react';
import {StatusBar} from 'react-native';
import {SafeAreaProvider} from 'react-native-safe-area-context';
import {NavigationContainer} from '@react-navigation/native';
import {Provider as PaperProvider} from 'react-native-paper';
import {Provider as StoreProvider} from 'react-redux';
import {store} from './src/store/store';
import {restoreToken} from './src/store/authSlice';
import AppNavigator from './src/navigation/AppNavigator';

function AppContent() {
  useEffect(() => {
    store.dispatch(restoreToken());
  }, []);

  return (
    <NavigationContainer>
      <AppNavigator />
    </NavigationContainer>
  );
}

export default function App() {
  return (
    <StoreProvider store={store}>
      <PaperProvider>
        <SafeAreaProvider>
          <StatusBar barStyle="dark-content" />
          <AppContent />
        </SafeAreaProvider>
      </PaperProvider>
    </StoreProvider>
  );
}
