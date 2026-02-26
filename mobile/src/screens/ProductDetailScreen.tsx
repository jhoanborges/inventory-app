import React from 'react';
import {View, ScrollView, StyleSheet} from 'react-native';
import {Card, Title, Paragraph, Badge, Divider, Button} from 'react-native-paper';
import type {Producto} from '../types';
import type {NativeStackNavigationProp} from '@react-navigation/native-stack';
import type {RouteProp} from '@react-navigation/native';

type Props = {
  route: RouteProp<{params: {producto: Producto}}, 'params'>;
  navigation: NativeStackNavigationProp<any>;
};

export default function ProductDetailScreen({route, navigation}: Props) {
  const {producto} = route.params;

  return (
    <ScrollView style={styles.container}>
      <Card style={styles.card}>
        <Card.Content>
          <Title>{producto.nombre}</Title>
          <Paragraph>SKU: {producto.sku}</Paragraph>
          {producto.barcode && <Paragraph>Código: {producto.barcode}</Paragraph>}
          <Divider style={styles.divider} />
          <Paragraph>Categoría: {producto.categoria ?? 'N/A'}</Paragraph>
          <Paragraph>Unidad: {producto.unidad_medida}</Paragraph>
          <Paragraph>
            Precio: {producto.precio ? `$${Number(producto.precio).toFixed(2)}` : 'N/A'}
          </Paragraph>
          <Divider style={styles.divider} />
          <View style={styles.stockRow}>
            <Paragraph>Stock Actual: </Paragraph>
            <Badge
              size={24}
              style={{
                backgroundColor:
                  producto.stock_actual <= producto.stock_minimo ? 'red' : 'green',
              }}>
              {producto.stock_actual}
            </Badge>
          </View>
          <Paragraph>Stock Mínimo: {producto.stock_minimo}</Paragraph>
        </Card.Content>
      </Card>

      {producto.lotes && producto.lotes.length > 0 && (
        <Card style={styles.card}>
          <Card.Content>
            <Title>Lotes</Title>
            {producto.lotes.map(lote => (
              <View key={lote.id} style={styles.loteItem}>
                <Paragraph>
                  {lote.numero_lote} - Cant: {lote.cantidad} - {String(lote.estado)}
                </Paragraph>
                {lote.fecha_vencimiento && (
                  <Paragraph style={styles.small}>
                    Vence: {lote.fecha_vencimiento}
                  </Paragraph>
                )}
              </View>
            ))}
          </Card.Content>
        </Card>
      )}

      <Button
        mode="contained"
        onPress={() =>
          navigation.navigate('Movimiento', {producto_id: producto.id})
        }
        style={styles.button}>
        Registrar Movimiento
      </Button>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, padding: 16, backgroundColor: '#f5f5f5'},
  card: {marginBottom: 16},
  divider: {marginVertical: 12},
  stockRow: {flexDirection: 'row', alignItems: 'center', marginBottom: 4},
  loteItem: {paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: '#eee'},
  small: {fontSize: 12, color: '#666'},
  button: {marginVertical: 16},
});
