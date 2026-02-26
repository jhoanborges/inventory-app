import React, {useState} from 'react';
import {View, StyleSheet} from 'react-native';
import {TextInput, Button, SegmentedButtons} from 'react-native-paper';

type Props = {
  onSubmit: (data: {
    tipo: 'entrada' | 'salida';
    cantidad: number;
    motivo?: string;
  }) => void;
  loading?: boolean;
};

export default function MovimientoForm({onSubmit, loading}: Props) {
  const [tipo, setTipo] = useState<'entrada' | 'salida'>('entrada');
  const [cantidad, setCantidad] = useState('');
  const [motivo, setMotivo] = useState('');

  const handleSubmit = () => {
    onSubmit({
      tipo,
      cantidad: parseInt(cantidad) || 0,
      motivo: motivo || undefined,
    });
  };

  return (
    <View style={styles.container}>
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
      <Button mode="contained" onPress={handleSubmit} loading={loading}>
        Registrar
      </Button>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {padding: 16},
  segment: {marginBottom: 16},
  input: {marginBottom: 16},
});
