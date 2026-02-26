import React, {useState} from 'react';
import {View, StyleSheet, Alert} from 'react-native';
import {TextInput, Button, Title, SegmentedButtons} from 'react-native-paper';
import {useAppDispatch} from '../store/hooks';
import {addMovimiento} from '../store/movimientosSlice';
import type {NativeStackNavigationProp} from '@react-navigation/native-stack';
import type {RouteProp} from '@react-navigation/native';

type Props = {
  route: RouteProp<{params: {producto_id: number}}, 'params'>;
  navigation: NativeStackNavigationProp<any>;
};

export default function MovimientoScreen({route, navigation}: Props) {
  const dispatch = useAppDispatch();
  const {producto_id} = route.params;
  const [tipo, setTipo] = useState<'entrada' | 'salida'>('entrada');
  const [cantidad, setCantidad] = useState('');
  const [motivo, setMotivo] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!cantidad || parseInt(cantidad) <= 0) {
      Alert.alert('Error', 'Ingresa una cantidad válida');
      return;
    }
    setLoading(true);
    try {
      await dispatch(
        addMovimiento({
          producto_id,
          tipo,
          cantidad: parseInt(cantidad),
          motivo: motivo || undefined,
        }),
      ).unwrap();
      Alert.alert('Éxito', 'Movimiento registrado', [
        {text: 'OK', onPress: () => navigation.goBack()},
      ]);
    } catch {
      Alert.alert('Error', 'No se pudo registrar el movimiento');
    }
    setLoading(false);
  };

  return (
    <View style={styles.container}>
      <Title style={styles.title}>Nuevo Movimiento</Title>

      <SegmentedButtons
        value={tipo}
        onValueChange={v => setTipo(v as 'entrada' | 'salida')}
        buttons={[
          {value: 'entrada', label: 'Entrada'},
          {value: 'salida', label: 'Salida'},
        ]}
        style={styles.segment}
      />

      <TextInput
        label="Cantidad"
        value={cantidad}
        onChangeText={setCantidad}
        keyboardType="numeric"
        mode="outlined"
        style={styles.input}
      />

      <TextInput
        label="Motivo (opcional)"
        value={motivo}
        onChangeText={setMotivo}
        mode="outlined"
        style={styles.input}
      />

      <Button
        mode="contained"
        onPress={handleSubmit}
        loading={loading}
        disabled={loading}
        style={styles.button}>
        Registrar
      </Button>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, padding: 24, backgroundColor: '#fff'},
  title: {marginBottom: 24, fontSize: 24},
  segment: {marginBottom: 16},
  input: {marginBottom: 16},
  button: {marginTop: 8},
});
