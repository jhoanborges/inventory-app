import React from 'react';
import {ScrollView, StyleSheet} from 'react-native';
import {Card, Title, Paragraph, Divider} from 'react-native-paper';
import type {Ruta} from '../types';
import type {RouteProp} from '@react-navigation/native';

type Props = {
  route: RouteProp<{params: {ruta: Ruta}}, 'params'>;
};

export default function RutaDetailScreen({route}: Props) {
  const {ruta} = route.params;

  return (
    <ScrollView style={styles.container}>
      <Card style={styles.card}>
        <Card.Content>
          <Title>{ruta.nombre}</Title>
          <Divider style={styles.divider} />
          <Paragraph>Origen: {ruta.origen}</Paragraph>
          <Paragraph>Destino: {ruta.destino}</Paragraph>
          <Paragraph>Estado: {String(ruta.estado)}</Paragraph>
          {ruta.vehiculo && <Paragraph>Vehículo: {ruta.vehiculo}</Paragraph>}
          {ruta.operador && (
            <Paragraph>Operador: {ruta.operador.name}</Paragraph>
          )}
          <Divider style={styles.divider} />
          {ruta.fecha_inicio && (
            <Paragraph>Inicio: {ruta.fecha_inicio}</Paragraph>
          )}
          {ruta.fecha_fin && <Paragraph>Fin: {ruta.fecha_fin}</Paragraph>}
        </Card.Content>
      </Card>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, padding: 16, backgroundColor: '#f5f5f5'},
  card: {marginBottom: 16},
  divider: {marginVertical: 12},
});
