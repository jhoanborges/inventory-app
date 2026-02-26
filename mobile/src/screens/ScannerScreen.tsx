import React, {useState} from 'react';
import {View, StyleSheet, Alert} from 'react-native';
import {TextInput, Button, Title} from 'react-native-paper';
import {scanBarcode} from '../services/api';
import type {NativeStackNavigationProp} from '@react-navigation/native-stack';

type Props = {
  navigation: NativeStackNavigationProp<any>;
};

export default function ScannerScreen({navigation}: Props) {
  const [barcode, setBarcode] = useState('');
  const [loading, setLoading] = useState(false);

  const handleScan = async () => {
    if (!barcode.trim()) {
      return;
    }
    setLoading(true);
    try {
      const {data} = await scanBarcode(barcode.trim());
      navigation.navigate('ProductDetail', {producto: data.data});
    } catch {
      Alert.alert('No encontrado', 'Producto no encontrado con ese código');
    }
    setLoading(false);
  };

  return (
    <View style={styles.container}>
      <Title style={styles.title}>Escanear Producto</Title>
      <TextInput
        label="Código de barras"
        value={barcode}
        onChangeText={setBarcode}
        mode="outlined"
        style={styles.input}
        onSubmitEditing={handleScan}
      />
      <Button
        mode="contained"
        onPress={handleScan}
        loading={loading}
        disabled={loading}
        style={styles.button}>
        Buscar
      </Button>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, padding: 24, backgroundColor: '#fff'},
  title: {marginBottom: 24, fontSize: 24, textAlign: 'center'},
  input: {marginBottom: 16},
  button: {marginTop: 8},
});
