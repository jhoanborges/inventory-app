import React, {useState} from 'react';
import {View, StyleSheet} from 'react-native';
import {TextInput, Button, Title, HelperText} from 'react-native-paper';
import {useAppDispatch, useAppSelector} from '../store/hooks';
import {loginThunk} from '../store/authSlice';

export default function LoginScreen() {
  const dispatch = useAppDispatch();
  const {loading, error} = useAppSelector(s => s.auth);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = () => {
    dispatch(loginThunk({email, password}));
  };

  return (
    <View style={styles.container}>
      <Title style={styles.title}>Inventario Bodega</Title>
      <TextInput
        label="Email"
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        autoCapitalize="none"
        mode="outlined"
        style={styles.input}
      />
      <TextInput
        label="Contraseña"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
        mode="outlined"
        style={styles.input}
      />
      {error && <HelperText type="error">{error}</HelperText>}
      <Button
        mode="contained"
        onPress={handleLogin}
        loading={loading}
        disabled={loading}
        style={styles.button}>
        Iniciar Sesión
      </Button>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, justifyContent: 'center', padding: 24, backgroundColor: '#fff'},
  title: {textAlign: 'center', marginBottom: 32, fontSize: 28},
  input: {marginBottom: 16},
  button: {marginTop: 8},
});
